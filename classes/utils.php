<?php
require_once ("sarok/log.class.php");
require_once ("sarok/context.class.php");
require_once ("sarok/dbmodel/sessionclass.class.php");
require_once ("singletonloader.class.php");
require_once ("sarok/exceptions/mysqlException.class.php");
require_once ("sarok/dbmodel/sql.class.php");
require_once ("sarok/dbmodel/dbfacade.class.php");
require_once ("sarok/dbmodel/sessionfacade.class.php");
require_once ("sarok/actionpages/ActionPage.class.php");
require_once ("sarok/actionpages/action.class.php");
$log = singletonloader :: getInstance("log");
function __autoload($class_name) {
	$log = singletonloader :: getInstance("log");
	$classPath["dbFacade"] = "dbmodel/dbfacade";
	$classPath["blogfacade"] = "dbmodel/blogfacade";
	$classPath["exportfacade"] = "dbmodel/exportfacade";
	$classPath["importfacade"] = "dbmodel/importfacade";
	$classPath["imagefacade"] = "dbmodel/imagefacade";
	$classPath["mailfacade"] = "dbmodel/mailfacade";
	$classPath["rssItem"] = "dbmodel/rssitem";
	$classPath["rssfacade"] = "dbmodel/rssfacade";
	$classPath["statsfacade"] = "dbmodel/statsfacade";
	$classPath["banfacade"] = "dbmodel/banfacade";
	$classPath["mysql"] = "dbmodel/sql";
	$classPath["mysqlException"] = "exceptions/mysqlException";
	$classPath["dbFacadeException"] = "exceptions/dbFacadeException";
	$classPath["DALException"] = "exceptions/DALException";
	$classPath["LoginFailedException"] = "exceptions/LoginFailedException";
	$classPath["dal"] = "dal/dal";
	$classPath["userDAL"] = "dal/UserDAL";
	$classPath["sessionDAL"] = "dal/sessionDAL";
	$classPath["textProcessor"] = "text/textProcessor";
	$classPath["monthstat"]="dbmodel/stats/monthstat";

	if(isset($classPath[$class_name]))
	{
	$hint = "../classes/sarok/".$classPath[$class_name].".class.php";

	$log->debug("autoload: searching for $class_name");

	if (class_hint($hint))
		return true;
	}
	if(strpos($class_name,"DAL"))
	   {
	   	//$log->debug("GFEIOWNGIOWENGWEIONIGOWE");
	   	$hint="../classes/sarok/dal/".$class_name.".class.php";
	   	if(class_hint($hint)) return true;
	   }

	if(strpos($class_name,"AP"))
	   {
	   	//$log->debug("GFEIOWNGIOWENGWEIONIGOWE");
	   	$hint="../classes/sarok/actionpages/".$class_name.".class.php";
	   	if(class_hint($hint)) return true;
	   }
	if(strpos($class_name,"Action"))
	   {
	   	//$log->debug("GFEIOWNGIOWENGWEIONIGOWE");
	   	$hint="../classes/sarok/actionpages/".$class_name.".class.php";
	   	if(class_hint($hint)) return true;
	   }

	if (strpos($class_name, "Exception")) {
		$hint = "../classes/sarok/exceptions/".$class_name.".class.php";
		if (class_hint($hint))
			return true;
	}

	$log->error("autoload: could not find file for the $class_name");
	return false;
}

function class_hint($hint) {
	$log = singletonloader :: getInstance("log");
	$log->debug("class_hint: hint is $hint");
	/*
	 * NOTICE: file_exists is not used since it is really slow
	 */
	if(file_exists($hint))
	try {
		require_once $hint;
		$log->debug("$hint is corrrect");
		return true;
	} catch (Exception $e) {
		$log->warning("$hint is incorrrect");
		return false;
	}

}

function gethost() {
	//echo "GLOBALS:".$GLOBALS['REMOTE_ADDR'];
	//return(gethostbyaddr($_SERVER['REMOTE_ADDR']));
	return ($_SERVER['REMOTE_ADDR']);
}

function putEditable($name,$value,$params,$idStyle="entry")
{
	global $editableType;
	$editableType=$idStyle;
	$context=singletonloader::getInstance("contextClass");
	$user=$context->user;
	if($user->wysiwyg!='N')
	{
	echo "<script>var nonEditable='true';</script>";
		echo "<textarea class=editable name='$name'>$value</textarea>";
	}
	else
	{
	echo "<script>var nonEditable='true';</script>";
		echo "<textarea class=nonEditable id='innereditable' name='$name' style='width: 95%;height: 300px'>$value</textarea>";
	}
}

function putEditable3($name,$value,$params,$idStyle="entry")
{
	global $xhtmlMode;
	$xhtmlMode=false;
	//$xhtmlMode=true;
	require("../www/fckeditable/fck/fckeditor.php");

$ed=new FCKeditor($name);

$ed->BasePath = '/fckeditable/fck/';
if($idStyle=="comment")
	$ed->Config['CustomConfigurationsPath']='/editable/commentConfig.js';
else
	$ed->Config['CustomConfigurationsPath']='/editable/entryConfig.js';
	
$ed->Value=$value;
$ed->Height = '400';

$ed->Create();
}

function putEditable2($name,$value,$params,$idStyle="entry")
{
	global $xhtmlMode;
	$xhtmlMode=false;
	//$xhtmlMode=true;
	$additionalParams=addslashes($params);
	$value=str_replace("<","&lt;",$value);
	$value=str_replace(">","&gt;",$value);
	require("../templates/editable/editable.php");
}

function splitByDates($data,$dateField="datum")
{
	if(!is_array($data) or !sizeof($data)) return $data;

	foreach($data as $value)
	{
		$dates=split(" ",$value[$dateField]);
		if(is_array($dates) and sizeof($dates>=2))
		{
			$value[$dateField]=$dates[1];
			$out[$dates[0]][]=$value;
		}
	}
return($out);
}

function getWeekDate($year,$month,$day)
{
//	return (0);
	$date=mktime(0,0,0,$day,$month,$year);
	$weekNum=date("w",$date);
	//echo $weekNum;
	$wn=($weekNum+6)%7;
	//echo $wn;
	return $wn;
}

function extractDate($datetime)
{
	$dat=explode(" ",$datetime);
	$date=explode("-",$dat[0]);
	$time=explode(":",$dat[1]);
	$out["year"]=$date[0];
	$out["month"]=$date[1];
	$out["day"]=$date[2];
	
	$out["hour"]=$time[0];
	$out["minute"]=$time[1];
	$out["second"]=$time[2];
	
	return $out;
}

function now(){return(date("Y-m-d G:i:s"));}
function now2(){return(date("Y-m-d"));}
function Yearmonth(){return(date("Y-m"));}

function human_date($date)
{
global $honapok;
$dates=explode("-",$date);
$out=$dates[2].". ".$honapok[(int)$dates[1]]." ".$dates[0];
return($out);
}

function human_time($date)
{
$dd=explode(" ",$date);
$out=$dd[1].", ".human_date($dd[0]);
return($out);
}

function weekend($num)
{
$n=($num-1)*7;
$result=mquery("select '2002-01-06' + interval $n day  as d");
$d=mysql_fetch_array($result);
return($d["d"]);
}

function year()
{
return(date("Y"));
}

function yearshort()
{
return(date("y"));
}

function month()
{
return(date("m"));
}

function day()
{
return(date("d"));
}

function dayInMonth($month, $year)
 {
    $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    if ($month != 2) return $daysInMonth[$month - 1];
    return (checkdate($month, 29, $year)) ? 29 : 28;
}

function weeknumber ($y, $m, $d) {
    $wn = strftime("%W",mktime(0,0,0,$m,$d,$y));
    $wn += 0; # wn might be a string value
    $firstdayofyear = getdate(mktime(0,0,0,1,1,$y));
    if ($firstdayofyear["wday"] != 1)    # if 1/1 is not a Monday, add 1
        $wn += 1;
    return ($wn);
}

function get_monday($week, $year=""){

       $first_date = strtotime("1 january ".($year ? $year : date("Y")));

          if(date("D", $first_date)=="Mon") {
               $monday = $first_date;
          } else {
               $monday = strtotime("next Monday", $first_date)-604800;
          }
          $plus_week = "+".($week-1)." week";

     return strtotime($plus_week, $monday);
}

function getMaxDaysInMonth($year,$month)
{
	$maxdays=array(31,28,31,30,31,30,31,31,30,31,30,31);
	$m=(int)$month;
	if($year%4) $maxdays[1]=29;
	
	return $maxdays[$m-1];
	
}

function get_day($dayofweek, $week, $year){
   $monday=get_monday($week,$year);
   $str="+ $dayofweek day";
	 return strtotime($str,$monday);
}

/*
Lenyeg:
Vesszuk a hetet, ahol kezdodik a honap.
Vesszuk a hetet, ahol vegzodik a honap.
kettes ciklussal (egyik hetek, masik napok szerint) vegigmegyunk:
for(het =startweek;het<endweek+1;het++)
   for(nap=0;nap<7;nap++)
	   do your fucking job
---------------
startweek=weeknumber($y,$m,1)
endweek=weeknumber($y,$m,DayInMonth($m,$y))

*/
function calendar($y,$m,$datelist)
{
global $honapok, $user, $blog, $grants, $dayofweek;

$startweek=weeknumber($y,$m,1);
$endweek=weeknumber($y,$m,dayInMonth($m,$y))+1;
$out="";
$out.="\n<table class='month'  cellspacing=0 cellpadding=0>\n";
for($day=0;$day<7;$day++)
    {
     if($day>4)      $out.="<tr class='weekend'>";
		 else
       $out.="<tr>";
		 $out.="<td class='DayName'>".$dayofweek[$day]."</td>";
       for($week=$startweek;$week<$endweek;$week++)
			     {
					 $t=get_day($day,$week,$y);
					 $strdate=date("Y-m-d",$t);
					 $dayofmonth=date("d",$t);
					 $monthofday=date("m",$t);
					 if($monthofday==$m) $id="id='$strdate'"; else $id=" ";
	  			 $class="class='active'";
  				 if($strdate==date("Y-m-d"))
  				 {
  				 		$class="class='current'";
  				 }
  				 if($monthofday!=$m) 
  				 {
  				 	         $class="class='oldmonth'";
  				 }
					 elseif($datelist!="null" && isset($datelist["$y-$m-$dayofmonth"]))
					  {
             			$class="title='".$datelist["$y-$m-$dayofmonth"]["numAll"]." bejegyzés'";
					   	$link=$datelist["$y-$m-$dayofmonth"]["link"];
					   	$dayofmonth="<a href='$link' >$dayofmonth</a>";
					  }
           $out.="<td $id $class>$dayofmonth</td>";
//$class="class='listed' descr='".$datelist["$y-$m-$dayofmonth"]."'";

					}
					$out.="</tr>\n";
//			 $out.=br();
		}
		$out.="</table>\n";
return($out);
}

function getTagClass($num,$min,$max)
{
	global $tagGrades;
	if($max-$min)
	{
		$q=($num-$min+1)/($max+1-$min);
		//echo "<br />\n$num, $q<br />\n";
		$q=$tagGrades*sqrt(sqrt($q));
		return((int)ceil($q));
	}
	else return(1);
}

function fserialize($obj,$fname)
{
		$str = serialize($obj);
  		$fp = fopen($fname, "w");
  		fwrite($fp, $str);
  		fclose($fp);
}

function funserialize($fname)
{
	if(file_exists($fname))
	{
		$str=implode("",file($fname));
		return(unserialize($str));
	}
	else
	{
		return false;
	}
}
	?>