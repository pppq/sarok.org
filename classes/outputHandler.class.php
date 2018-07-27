<?php
class outputHandler {
private $buf;
private $log;
    function outputHandler() {
    	$this->log=singletonloader::getInstance("log");
    	$this->log->debug2("init outputHandler");
    	$this->buf="";
    	ob_start();
    	ob_clean();
    }
    function getContent($file,$data)
    {
    	global $skinName;
    	$this->log->debug2("outputHandler -> getContent($file)");
    	$this->buf.=ob_get_contents();
    	ob_clean();
    	if(file_exists($file))
    	{
			extract($data);
			require($file);
    	}
    	else
    	{
    		$this->log->error("$file does not exist!");
    	}
    	$buf=ob_get_contents();
		ob_clean();
		$this->log->debug("<- outputHandler (contents length is ".strlen($buf)." bytes");
		//return "";
		return($buf);
    }
    function getBuffer()
    {
    	$this->buf.=ob_get_contents();
    	ob_clean();
    	//return "".""."";
    	//$this->buf=substr($this->buf,3);
    	return($this->buf);
    }
}
?>