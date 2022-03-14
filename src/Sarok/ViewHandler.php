<?php namespace Sarok;

class ViewHandler
{
    /** 
     * tile name => array of actions
     * @var array<string, array<string>> 
     */
    private array $actions = array();

    /**
     * action name => action data (as an associative array)
     * @var array<string, mixed>
     */ 
    private array $data = array();
    
    /**
     * tile name => tile content + any extra variables used in the main template (index.php)
     * @var array<string, mixed>
     */
    private array $viewData = array();
    
    private string $templateName;
    private TemplateRenderer $templateRenderer;
    private Logger $logger;

    public function __construct(Logger $logger, TemplateRenderer $templateRenderer)
    {
        $this->logger = $logger;
        $this->templateRenderer = $templateRenderer;
        $this->logger->debug("ViewHandler initialized");
    }

    public function setTemplate(string $templateName) : void
    {
        if (file_exists("../templates/$templateName/index.php")) {
            $this->logger->debug("Using template '$templateName'");
            $this->templateName = $templateName;
        } else {
            $this->logger->warning("Template '$templateName' does not exist, using default");
            $this->templateName = 'default';
        }
    }

    public function addAction(string $tile, string $action) : void
    {
        if (!isset($this->actions[$tile])) {
            $this->actions[$tile] = array($action);
        } else {
            $this->actions[$tile][] = $action;
        }
    }
    
    /**
     * @param array<string, mixed> $actions
     */
    public function setData(array $actions) : void
    {
        foreach ($actions as $action => $variables) {
            $this->data[$action] = $variables;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setViewData(string $key, $value) : void
    {
        $this->viewData[$key] = $value;
    }
    
    /**
     * @param array<string, mixed> $viewData
     */
    public function mergeViewData(array $viewData) : void
    {
        $this->viewData = array_merge($this->viewData, $viewData);
    }
    
    private function renderAction(string $action) : string
    {
        $templatePath = "../templates/{$this->templateName}/$action.php";

        if (file_exists($templatePath)) {
            $this->logger->debug("Using action template '$templatePath'");
        } else {
            $this->logger->warning("Action template '$templatePath' does not exist, using default");
            $templatePath = "../templates/default/$action.php";
        }
        
        return $this->templateRenderer->render($templatePath, $this->data[$action]);
    }

    public function render() : string
    {
        // Concatenate rendered output for each action in tile
        foreach ($this->actions as $tile => $tileActions) {
            if (!isset($this->viewData[$tile])) {
                $this->viewData[$tile] = "";
            }
            
            foreach ($tileActions as $action) {
                $this->viewData[$tile] .= $this->renderAction($action);
            }
        }

        $templatePath = "../templates/{$this->templateName}/index.php";
        return $this->templateRenderer->render($templatePath, $this->viewData);
    }
}
