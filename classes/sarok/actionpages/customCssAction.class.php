<?php

class customCssAction extends Action{

 	function execute()
 	{
		if($this->context->user->ID==$this->context->blog->ID or $this->context->user->ID==1)
			$css=$this->context->blog->css;
		$out["css"]=$css;
		return $out;
 	}
}
?>