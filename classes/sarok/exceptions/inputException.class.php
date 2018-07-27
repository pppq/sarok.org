<?php
class inputException extends Exception{
public $msg;
		function inputException($msg)
		{
			$this->msg=$msg;
		}
}
?>
