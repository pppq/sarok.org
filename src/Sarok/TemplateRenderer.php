<?php namespace Sarok;

class TemplateRenderer {
    
    private Logger $log;
    
    public function __construct(Logger $log) {
    	$this->log = $log;
    	$this->log->debug("TemplateRenderer initialized");
    }
    
    public function render(string $templatePath, array $variables) : string {
    	$this->log->debug("Rendering content using template '$templatePath'");
    	
    	if (!file_exists($templatePath)) {
    		$this->log->error("Template '$templatePath' does not exist, returning empty content");
    	    return "";
    	}
    	
   	    ob_start();
		extract($variables);
		require($templatePath);
    	$content = ob_get_clean();

		$this->log->debug("Rendering '$templatePath' completed, content length is " . strlen($content) . " bytes");
		return $content;
    }
}
