<?php
class singletonloader {
	const EXCEPTION_NO_CLASS = 1;

	protected static $_instances = array ();

	public static function getInstance($class) {
		if (!isset (self :: $_instances[$class])) {
			if (!class_exists($class))
				throw (new Exception(__CLASS__.': Requested class does not exist.', self :: EXCEPTION_NO_CLASS));
			self :: $_instances[$class] = new $class ();
		}
		return self :: $_instances[$class];
	} // getInstance

}
?>