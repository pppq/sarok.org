<?php namespace Sarok;

class ViewHandler {
    
    // tile name => array of actions
    private array $tiles = array();
    // action name => key => value 
    private array $data = array();

    private string $templateName;
    private TemplateRenderer $templateRenderer;
    private Logger $logger;

    public function __construct(Logger $logger, TemplateRenderer $templateRenderer) {
        $this->logger = $logger;
        $this->templateRenderer = $templateRenderer;
        $this->logger->debug("ViewHandler initialized");
    }

    public function setTemplate(string $templateName) {
        if (file_exists("../templates/$templateName/index.php")) {
            $this->logger->debug("Using template '$templateName'");
            $this->templateName = $templateName;
        } else {
            $this->logger->warning("Template '$templateName' does not exist, using default");
            $this->templateName = 'default';
        }
    }

    public function addAction(string $tile, string $action) {
        $this->tiles[$tile][] = $action;
    }
    
    public function setData(array $actions) {
        foreach ($actions as $action => $variables) {
            $this->data[$action] = $variables;
        }
    }

    private function renderAction($action) : string {
        $templatePath = "../templates/{$this->templateName}/$action.php";
        if (file_exists($templatePath)) {
            $this->logger->debug("Using action template '$templatePath'");
        } else {
            $this->logger->warning("Action template '$templatePath' does not exist, using default");
            $templatePath = "../templates/default/$action.php";
        }
        
        return $this->templateRenderer->render($templatePath, $this->data[$action]);
    }

    public function render() : string {
        $tileVariables = array();
        
        foreach ($this->tiles as $tile => $actions) {
            $tileVariables[$tile] = "";
            foreach ($actions as $action) {
                $tileVariables[$tile] .= $this->renderAction($action);
            }
        }

        $templatePath = "../templates/{$this->templateName}/index.php";
        return $this->templateRenderer->render($templatePath, $tileVariables);
    }
}
