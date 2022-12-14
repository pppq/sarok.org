<?php declare(strict_types=1);

namespace Sarok;

/**
 * Renders text to PHP templates using variable substitution.
 */
final class TemplateRenderer
{
    private Logger $logger;
    
    public function __construct(Logger $logger) 
	{
    	$this->logger = $logger;
    	$this->logger->debug("TemplateRenderer initialized");
    }
    
    public function render(string $templatePath, array $variables) : string 
	{
    	$this->logger->debug("Rendering content using template '${templatePath}'");
    	
    	if (!file_exists($templatePath)) {
    		$this->logger->error("Template '${templatePath}' does not exist, returning empty content");
    	    return "";
    	}
    	
		try {
			ob_start();
			extract($variables);
			require($templatePath);
		} finally {
			// Content can be partial in case an error occurs, but we still want to close the output buffer
			$content = ob_get_clean();
			$this->logger->debug("Rendering '${templatePath}' completed, content length is " . strlen($content) . " bytes");
			return $content;
		}
    }
}
