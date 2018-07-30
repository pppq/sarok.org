<?
/**
 * Module includes fase
 */

 //error_reporting(false);
require_once "../config.php";
try{
require_once "../classes/utils.php";
require_once "../classes/sarok/context.class.php";
require_once "../classes/outputHandler.class.php";
require_once "../classes/sarok/viewHandler.class.php";

/**
 * Initialization fase
 */

$log = singletonloader :: getInstance("log");
$view=singletonloader:: getInstance("viewHandler");
$buf= singletonloader :: getInstance("outputHandler");
$context = singletonloader :: getInstance("contextClass");
$log->debug2("starting application");
$db = singletonloader :: getInstance("dbFacade");
$sessionFacade = singletonloader :: getInstance("sessionfacade");

if (isset($_SERVER["HTTPS"])) {
  $protocol = "https";
} else {
  $protocol = "http";
}

/**
 * Session initialization fase
 */
$log->debug2("init session");
$context->session=new sessionclass(isset($_COOKIE["your_mommy"])?$_COOKIE["your_mommy"]:"",$_SERVER["REMOTE_ADDR"],"/",$db_host);
$context->session->sendHeaders();
$log->debug2("session inited");


/**
 *  Execution fase
 */
$APclass=$context->parseURL(isset($_GET["p"])?$_GET["p"]:"")."AP";

$log->debug2("Executing Action Page");
$AP=new $APclass();
$tileList["menu"][]="menu";
$tileList["logout"][]="logoutform";
$tileList["leftMenu"][]="leftMenu";
$tileList["friendlist"]=array();
if($context->props["loggedin"]) $tileList["friendlist"][]="friendlist";
//$tileList["header"][]="customCss";
$log->debug2("Running canRun() for $APclass");
if(!$AP->canRun()) $AP=new errorAP();
$altList=$AP->init($context->params);
$tileList=array_merge($tileList,$altList);
$tileList["newmail"]=array();
if($context->props["loggedin"]) $tileList["newmail"][]="checkMail";
//print_r($tileList);
$data=$AP->execute($tileList);

$template=$AP->getTemplate();
$log->debug2("Action Page executed");


/**
 * Display fase
 */
$xhtmlMode=true;
 $log->debug2("Template is $template");
$view->setTemplate($template);
$view->addActions($data);
header("Content-Type: text/html; charset=utf-8");
$outstr=$view->process($tileList);

/**
 * Output fase
 */
//ob_clean();
$tp = singletonloader :: getInstance("textProcessor");
if($template!="empty" and $tidy_check and $xhtmlMode)
	$out=$tp->tidy($outstr);
else
	$out=$outstr;
//$out=$buf->getBuffer();
ob_clean();
//$out=substr($out,1);
echo $out;

/**
 * finalization fase
 */
singletonloader :: getInstance("statsfacade")->putLogRecord();
singletonloader :: getInstance("mysql")->close();
$log->info("end");
}
catch(Exception $e)
{
	$traceList=$e->getTrace();
	$str=date("Y-m-d G:i:s")."\n\n";
	
		$str.=$e->getMessage()."\n\n";
		for($i=sizeof($traceList)-1;$i>=0;$i--)
		{
			$trace=$traceList[$i];
			$file=explode("\\",$trace["file"]);
			$filename=$file[sizeof($file)-1];
			$filename=str_replace(".php","",$filename);
			$filename=str_replace(".class","",$filename);
			$str.=$filename.":".$trace["function"].":".$trace["line"]." -->\n";
		}
		
	foreach($_SERVER as $k=>$v)
	{
		$str.="$k: $v\n";
	}
	
	foreach($_POST as $k=>$v)
	{
		$str.="$k: $v\n";
	}
	foreach($_GET as $k=>$v)
	{
		$str.="$k: $v\n";
	}
	foreach($_COOKIE as $k=>$v)
	{
		$str.="$k: $v\n";
	}
	require("error.php");
	//echo $str;
	mail($system_email,"Exception page on sarok.org","$str");
	
}
?>
