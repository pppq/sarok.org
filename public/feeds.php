<?php
/**
 * Module includes fase
 */
require_once "../config.php";
$log_level=1;
$general_logfile="../logs/feed.txt";

require_once "../src/utils.php";
/**
 * Initialization fase
 */
$logger=singletonloader:: getInstance("log");
//$logger->mail("Starting feed update","feeds");
	$rss=singletonloader:: getInstance("rssfacade");
	try{
	$rss->updateFeeds();
	}
	catch(Exception $e)
	{
		$logger->security("Error: ".$e->getTrace().", \n\n".$e->getMessage(),"feeds");
	}




?>