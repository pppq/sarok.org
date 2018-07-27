<?php
/**
 * Module includes fase
 */
require_once "../config.php";
$log_level=1;
$general_logfile="../logs/stats.txt";

require_once "../classes/utils.php";
/**
 * Initialization fase
 */
$logger=singletonloader:: getInstance("log");
//$logger->mail("Starting feed update","feeds");
	$stats=singletonloader:: getInstance("statsfacade");
	try{
	$stats->collectData();
//	$logger->info("collecting blog information");
	//$stats->collectBlogInformation(5,"2006","10");
//	print_r($stats->getUserMonth(5,"2006","10"));
	}
	catch(Exception $e)
	{
		$logger->security("Error: ".$e->getTrace().", \n\n".$e->getMessage(),"feeds");
	}




?>