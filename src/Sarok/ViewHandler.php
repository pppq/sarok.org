<?php declare(strict_types=1);

namespace Sarok;

/**
 * Renders text to smaller templates (tiles), then combines the output using another template.
 */
final class ViewHandler 
{
    public const TEMPLATE_BASE_PATH = '../templates';
    
    private const DEFAULT_TEMPLATE_PATH = self::TEMPLATE_BASE_PATH . '/default';
    private const MAIN_TEMPLATE = '/index.php';
    private const EMPTY_TEMPLATE = '/empty.php';

    /** 
     * tile name => array of action (name)s
     * @var array<string, array<string>> 
     */
    private array $tiles = array();

    /**
     * action name => action data as an associative array
     * @var array<string, array<string, mixed>>
     */ 
    private array $actionData = array();
    
    /**
     * tile name => tile content + any extra variables used in the main template (index.php)
     * @var array<string, mixed>
     */
    private array $tileData = array();
    
    private string $templatePath;
    private TemplateRenderer $templateRenderer;
    private Logger $logger;

    public function __construct(Logger $logger, TemplateRenderer $templateRenderer) 
    {
        $this->logger = $logger;
        $this->templateRenderer = $templateRenderer;
        $this->logger->debug("ViewHandler initialized");
    }

    public function setTemplatePath(string $templatePath) : void
    {
        if (file_exists($templatePath . self::MAIN_TEMPLATE)) {
            $this->logger->debug("Using template path '${templatePath}'");
            $this->templatePath = $templatePath;
        } else {
            $this->logger->warning("Template '${templatePath}' does not exist, using default path");
            $this->templatePath = self::DEFAULT_TEMPLATE_PATH;
        }
    }

    public function addAction(string $tile, string $action) : void
    {
        if (!isset($this->tiles[$tile])) {
            $this->tiles[$tile] = array($action);
        } else {
            $this->tiles[$tile][] = $action;
        }
    }
    
    /**
     * @param array<string, array<string, mixed>> $actionData
     */
    public function putAllActionData(array $actionData) : void
    {
        $this->actionData = array_replace($this->actionData, $actionData);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function putTileData(string $key, mixed $value) : void
    {
        $this->tileData[$key] = $value;
    }
    
    /**
     * @param array<string, mixed> $tileData
     */
    public function putAllTileData(array $tileData) : void
    {
        $this->tileData = array_replace($this->tileData, $tileData);
    }
    
    private function renderAction(string $action) : string
    {
        $actionTemplatePath = $this->templatePath . "/${action}.php";

        if (file_exists($actionTemplatePath)) {
            // Plan A is to use the named template from the specified template directory
            $this->logger->debug("Using action template '${actionTemplatePath}'");
        } else {
            // Plan B is to use the default template directory and the named template
            $this->logger->warning("Action template '${actionTemplatePath}' does not exist, using default");
            $actionTemplatePath = self::DEFAULT_TEMPLATE_PATH . "/${action}.php";

            // Plan C is to render to an empty template
            if (!file_exists($actionTemplatePath)) {
                $actionTemplatePath = self::DEFAULT_TEMPLATE_PATH . self::EMPTY_TEMPLATE;
            }
        }
        
        $actionData = $this->actionData[$action];
        return $this->templateRenderer->render($actionTemplatePath, $actionData);
    }

    public function render() : string
    {
        foreach ($this->tiles as $name => $actions) {
            // We need this key to be a string; initialize it to an empty one if not already defined
            if (!isset($this->tileData[$name]) || !is_string($this->tileData[$name])) {
                $this->tileData[$name] = '';
            }
            
            // Concatenate rendered output for each action in tile
            foreach ($actions as $action) {
                $this->tileData[$name] .= $this->renderAction($action);
            }
        }

        // Render the gathered output using the main template
        $mainTemplatePath = $this->templatePath . '/index.php';
        return $this->templateRenderer->render($mainTemplatePath, $this->tileData);
    }
}
