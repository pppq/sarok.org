<?php
header("Content-Type: text/html; charset=utf-8\n\n");
/**
 * Module includes fase
 */
require_once "../config.php";
try{
require_once "../src/utils.php";
/**
 * Initialization fase
 */

$log = singletonloader :: getInstance("log");

$log->debug("Calling ".$_POST["action"]);
$log->debug("Text is ".$_POST["text"]);
if (!isset ($_POST["text2"]))
	$_POST["action"] ($_POST["text"]);
else
	$_POST["action"] ($_POST["text"], $_POST["text2"]);
	
}
catch(Exception $e)
{
	$traceList=$e->getTrace();
	$str=date("Y-m-d G:i:s")."\n\n";
		for($i=sizeof($traceList)-1;$i>=0;$i--)
		{
			$trace=$traceList[$i];
			$file=explode("\\",$trace["file"]);
			$filename=$file[sizeof($file)-1];
			$filename=str_replace(".php","",$filename);
			$filename=str_replace(".class","",$filename);
			$str.=$filename.":".$trace["function"].":".$trace["line"]." -->\n";
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
	//require("error.php");
	echo "Hiba";
	mail("system@sarok.org","Exception page on sarok.org","$str");
	
}

	
function format($text) {
	global $log;
	//if(isset($_POST["Text"])) $text=$_POST["Text"];
	//str_replace("%26","&",$text);
	
	$log->debug2("Start");
	$tp = singletonloader :: getInstance("textProcessor");
	$log->debug2("preformatting");
	$text = $tp->preFormat($text);
	$log->debug2("postformatting");
	$text = $tp->postFormat($text);
	$log->debug2("cleaning up");
	$text = $tp->cleanUp($text);
	$log->debug2("Finished cleanup");
	echo $text;
}

function saveBackup($text) {
	global $db_host;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$context->session->sendHeaders();
	$log->debug2("session inited");

	$user = $context->user;
	if ($user->login != 'Anonim') {
		$user->backup = addslashes($text);
		$user->commit();
		echo now();
	} else {
		echo "Server hiba miatt ki lettél léptetve. Legyszi, másold ki a szöveget és lépj be újra";
	}
}

function getComments($text) {
	global $db_host, $cookiedomain;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$sf = singletonloader :: getInstance("sessionFacade");
	$context->session->sendHeaders();
	$log->debug2("session inited");

	$user = $context->user;
	if ($user->ID == '1') {
		echo "nem vagy belépve!";
		return;
	}
	if ($user->commentsLoaded == '2') {
		echo "<span>A hozzaszolasok listája épp készül, egy kis türelmet kérek</span>";
		return;
	}
	$comments = $sf->getCachedComments($user->ID);
	require ("../templates/default/commentlist.template.php");
}

function getEntries($text) {
	global $db_host, $cookiedomain;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$sf = singletonloader :: getInstance("sessionFacade");
	$context->session->sendHeaders();
	$log->debug2("session inited");

	$user = $context->user;
	if ($user->ID == '1') {
		echo "nem vagy belépve!";
		return;
	}
	if ($user->entriesLoaded == '2') {
		echo "<span>A bejegyzések listája épp készül, egy kis türelmet kérek</span>";
		return;
	}
	$entries = $sf->getCachedEntries($user->ID);
	require ("../templates/default/entrylist.template.php");
}

function getCommentsOfEntries($text) {
	global $db_host, $cookiedomain;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$sf = singletonloader :: getInstance("sessionFacade");
	$context->session->sendHeaders();
	$log->debug2("session inited");

	$user = $context->user;
	if ($user->ID == '1') {
		echo "<span>nem vagy belépve!</span>";
		return;
	}
	if ($user->commentsOfEntriesLoaded == '2') {
		echo "<span>A hozzaszolasok listája épp készül, egy kis türelmet kérek</span>";
		return;
	}
	$commentsOfEntries = $sf->getCommentsOfEntries($user->ID);
	require ("../templates/default/commentsofentrieslist.template.php");
}

function getMyComments($text) {
	global $db_host;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$sf = singletonloader :: getInstance("sessionFacade");
	setcookie("your_mommy", $context->session->ID, time() + 7200);
	$log->debug2("session inited");

	$user = $context->user;
	if ($user->ID == '1') {
		echo "<span>nem vagy belépve!</span>";
		return;
	}
	if ($user->myCmmentsLoaded == '2') {
		echo "<span>A hozzaszolasok listája épp készül, egy kis türelmet kérek</span>";
		return;
	}
	$myComments = $sf->getMyComments($user->ID);
	require ("../templates/default/mycomments.template.php");
}

function getUsers($text) {
	$db = singletonloader :: getInstance("mysql");
	$text = addslashes($text);
	$q = "select login from users where login like '$text%' order by login limit 10";
	$rows = $db->queryall($q);
	for ($i = 0; $i < sizeof($rows); $i ++) {
		echo "<li title='".$rows[$i]["login"]."'>".$rows[$i]["login"]."</li>\n";
	}
}

function getCity($text) {
	$db = singletonloader :: getInstance("mysql");
	$text = addslashes($text);
	$q = "select distinct value from userdata where value like '$text%' and name='city' order by login limit 10";
	$rows = $db->queryall($q);
	for ($i = 0; $i < sizeof($rows); $i ++) {
		echo "<li title='".$rows[$i]["value"]."'>".$rows[$i]["value"]."</li>\n";
	}
}

function getAvailableBlogs($text, $num) {
	global $db_host, $cookiedomain;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$sf = singletonloader :: getInstance("sessionFacade");
	$context->session->sendHeaders();
	$log->debug2("session inited");

	$db = singletonloader :: getInstance("mysql");

	$user = $context->user;
	$login = $user->login;
	$ID = $user->ID;
	if ($user->ID == '1') {
		echo "<span>nem vagy belépve!</span>";
		return;
	}

	$q = "select login from users
	where login like '$text%' and
	(
	login='$login' or
	ID in (
	select userID from userdata where name='blogAccess' and (
	value='registered' or (value='friends' and userID in (select friendOf from friends where userID='$ID'))
	)
	)
	) limit 10";
	$rows = $db->queryall($q);
	for ($i = 0; $i < sizeof($rows); $i ++) {
		echo "<li title='".$rows[$i]["login"]."'>".$rows[$i]["login"]."</li>\n";
	}
}

function findTagNum($text, $pos) {
	$left = substr($text, 0, $pos +1);
	$tags = split("[ ,;]+", $left);
	return (sizeof($tags) - 1);
}

function getTags($text, $pos) {
	$db = singletonloader :: getInstance("mysql");
	$tags = split("[ ,;]+", $text);
	if ($pos <= 1)
		$tagNum = 0;
	else
		$tagNum = findTagNum($text, $pos);
	$curTag = $tags[$tagNum];
	$q = "select distinct Name as name from categories where Name like '$curTag%' order by Name limit 10";
	$rows = $db->queryall($q);
	for ($i = 0; $i < sizeof($rows); $i ++) {
		$tmpTags = array ();
		$tmpTags = $tags;
		$tmpTags[$tagNum] = $rows[$i]["name"];
		$tmpTags = array_unique($tmpTags);
		$title = implode(", ", $tmpTags);
		echo "<li title='$title'>".$rows[$i]["name"]."</li>\n";
	}
}

function getUserList($text, $pos) {
	$db = singletonloader :: getInstance("mysql");
	$tags = split("[ ,;]+", $text);
	if ($pos <= 1)
		$tagNum = 0;
	else
		$tagNum = findTagNum($text, $pos);
	$curTag = $tags[$tagNum];
	$q = "select distinct login as name from users where login like '$curTag%' order by login limit 10";
	$rows = $db->queryall($q);
	for ($i = 0; $i < sizeof($rows); $i ++) {
		$tmpTags = array ();
		$tmpTags = $tags;
		$tmpTags[$tagNum] = $rows[$i]["name"];
		$tmpTags = array_unique($tmpTags);
		$title = implode(", ", $tmpTags);
		echo "<li title='$title'>".$rows[$i]["name"]."</li>\n";
	}
}

function delImage($filename) {
	global $db_host, $cookiedomain;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$sf = singletonloader :: getInstance("sessionFacade");
	$context->session->sendHeaders();
	$log->debug2("session inited");

	$im = singletonloader :: getInstance("imagefacade");
	if ($im->delImage($filename, $context->user->ID))
		echo "$filename törölve";
	else
		echo "Hiba! $filename nem letezik";
}

function checkLogin($login) {
	$db = singletonloader :: getInstance("mysql");
	$login = strtolower($login);
	if (strlen($login) < 2 || strlen($login) > 15) {
		echo "<span class=error>Nem megmondtam hogy max 15 karakter es min 2 karakter lehet a hossza?</span>";
		return;
	}
	if (!ereg("^[a-z][a-z0-9_]{1,14}$", $login)) {
		echo "<span class=error>Valamit elcseszted. Mondjuk vannak benne ekezetek? Vagy szammal kezdodik? Mondok valamit: <b>^[a-z][a-z0-9_]{1,14}\$</b></span>";
		return;
	}
	$regs = array ();
	if (ereg("((nyuszi)|(angyal)|(lany)|(angel))", $login, $regs)) {
		echo "<span class=error>Tele vagyunk {$regs[1]} felhasználókkal, legyél már egy picit kreativabb. Az nem art, ha blogot akarsz vezetni.</span>";
		return;
	}
	if (ereg("(szar)|(punci)|(pocs)|(fasz)|(picsa)|(geci)|(segg)", $login)) {
		echo "<span class=error>Ez egy beteg loginnev, $login. Ezt nem fogadom el.</span>";
		return;
	}

	$q = "select count(Login) as num from users where login='$login'";
	$number = $db->querynum($q);
	if ($number > 0) {
		echo "<span class=error>Sajnálom, $login már foglalt. Probálkozz egy másik névvel.</span>";
	} else {

		if (ereg("([6-9][0-9])$", $login, $regs))
			echo "Tippelek: ". ((int) ((int) year() - 1900 - (int) $regs[1]))." éves vagy, $login. Amugy jo lesz loginnevnek. Mondjuk röhögni fognak, ám legyen, ha ragaszkodz hozzá.";
		elseif (ereg("[0-9]", $login)) echo "Ezen a felhasználónéven röhögni fognak, ám legyen, ha ragaszkodz hozzá";
		elseif (ereg("(buzi)", $login)) echo "Nem buzulunk! Buzulas tilos!";
		else
			echo "Ez a felhasználónév nagyon szep, $login!";
	}

}

function checkEmail($email) {
	$email = strtolower($email);
	if (!ereg("^.+@.+\..+$", $email)) {
		echo "<span class=error>Ha ez Neked egy email cim, akkor én egy helikopter vagyok.</span>";
		return;
	}
	echo "Köszönöm!";
}

function checkPass($pass1, $pass2) {

	if ($pass1 != $pass2) {
		echo "<span class=error>A ket jelszonak meg kellene jegyezni. A saját érdekedben, értem?</span>";
		return;
	}
	if (strlen($pass1) < 3) {
		echo "<span class=error>A jelszo nagyon rovid, enyhen szolva.</span>";
		return;
	}
	echo "Köszönöm! Nagyon szep jelszavaid vannak!";
}

function removeComment($ID) {
	global $db_host, $cookiedomain;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$bf = singletonloader :: getInstance("blogfacade");
	$db = singletonloader :: getInstance("mysql");
	$context->session->sendHeaders();
	$log->debug2("session inited");

	$comments = $db->queryone("select * from comments where ID='$ID'");
	$entry = $db->queryone("select * from entries where ID='".$comments["entryID"]."'");
	if ($bf->canDeleteComment($comments, $entry, $context->user->ID)) {
		$bf->removeComment($ID);
		echo "<span class=info>$ID torolve</span>";
	} else {
		$log->security("Unallowed attempt to delete comment $ID");
		echo "<span class=error>Hiba! Nem torolhetem a hozzaszolast!</span>";
	}
}

function removePrivate($ID) {
	global $db_host, $cookiedomain;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$mf = singletonloader :: getInstance("mailfacade");
	$context->session->sendHeaders();
	$log->debug2("session inited");

	if ($mf->deleteMailByUserID($ID, $context->user->ID)) {
		$log->info("Mail $ID deleted");
		echo "<span class=info>$ID törölve. <a href=/mail/>Vissza</a></span>";
	} else {
		$log->security("Unallowed attempt to delete private $ID");
		echo "<span class=error>Hiba! Nem torolhetem a privátot!</span>";
	}
}

function performAction($params) {
	global $db_host, $cookiedomain;
	global $log;
	require_once "../src/context.class.php";
	require_once "../src/outputHandler.class.php";
	require_once "../src/viewHandler.class.php";
	$view = singletonloader :: getInstance("viewHandler");
	$buf = singletonloader :: getInstance("outputHandler");
	$context = singletonloader :: getInstance("contextClass");
	$log->debug2("starting application");
	$db = singletonloader :: getInstance("dbFacade");
	$sessionFacade = singletonloader :: getInstance("sessionfacade");

	/**
	 * Session initialization fase
	 */
	$log->debug2("init session");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$context->session->sendHeaders();
	$log->debug2("session inited");

	/**
	 *  Execution fase
	 */
	$APclass = $context->parseURL($params)."AP";
	$log->debug2("Executing Action Page");
	$AP = new $APclass ();
	if (!$AP->canRun())
		$AP = new errorAP();
	$tileList = $AP->init($context->params);

	//print_r($tileList);
	$data = $AP->execute($tileList);
	$view->setTemplate("empty");
	$view->addActions($data);
	echo $view->process($tileList);
	$out = $buf->getBuffer();
	echo $out;
}

function performActionInFrame($params) {
	echo "<iframe style='width:100%;height:100%;' src=$params ></iframe>";
}

function checkRss($rssLoc) {
	//error_reporting (false);
	$rss = singletonloader :: getInstance("rssItem");
	if (strlen($rssLoc) < 5) {
		return;
	}
	$val = $rss->readXML(trim($rssLoc));
	if ($val == 0) {
		echo "<span class=info>{$rss->title}, {$rss->link}, {$rss->description}</span>";
	}
	elseif ($val == 2) {
		echo "<span class=error>Ez a feed tok jo lenne, de sajnos nem tudtunk kiolvasni a bejegyzesekhez tartozo datumokat, es anelkul a sarkon nem megy. Kerd meg a blogod adminjat, hogy csinaljon valamit.</span>";
	} else {
		echo "<span class=error>Sajnalom, ez a feed nem lesz jo.</span>";
	}
}

function setFavourite($entryID) {
	global $db_host, $cookiedomain;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$sf = singletonloader :: getInstance("sessionFacade");
	$bf = singletonloader :: getInstance("blogfacade");
	$db = singletonloader :: getInstance("mysql");

	$context->session->sendHeaders();
	$log->debug2("session inited");
	$userID = $context->user->ID;
	$isFavourite = $bf->updateFavourite($userID, $entryID);
	//sleep(1);
	if ($isFavourite) {
		$log->debug("Favourite exists, deleting it");
		$q = "delete from favourites where userID='$userID' and entryID='$entryID' limit 1";
?><img src=/images/bookmark.gif border=0 title="könyvjelzökbe" ><?


	} else {
		$log->debug("Favourite does not exist, adding it");
		$q = "insert into favourites (userID,entryID,lastVisited) values ('$userID','$entryID', now()) on duplicate key update lastVisited=now()";
?><img src=/images/unbookmark.gif border=0 title="törlés a könyvjelzökből" ><?

	}
	$db->mquery($q);
}

function checkMail()
{
	global $db_host, $cookiedomain,$refreshTime;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$sf = singletonloader :: getInstance("sessionFacade");
	$context->session->sendHeaders();
	$log->debug2("session inited");

	$user = $context->user;
	if ($user->ID == '1') {
		echo "<span>nem vagy belépve!</span>";
		return;
	}
	
	$out=singletonloader::getInstance("checkMailAction")->execute();
	extract($out);
	$commentsOfEntries = $sf->getCommentsOfEntries($user->ID);
	require ("../templates/default/checkMail.php");
}

function getUserMonthStat($yearmonth)
{
	global $db_host, $cookiedomain, $honapok;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	$sf = singletonloader :: getInstance("sessionFacade");
	$stats = singletonloader :: getInstance("statsfacade");
	$context->session->sendHeaders();
	$log->debug2("session inited");
	
	$yearmonth=explode("-",$yearmonth);
	$stats->collectBlogMonthInformation($context->user->ID, $yearmonth[0], $yearmonth[1]);
	$obj=$stats->getUserMonth($context->user->ID, $yearmonth[0], $yearmonth[1]);
	//echo $yearmonth[0]." ".$yearmonth[1];
	$honapneve=$yearmonth[0]." ".$honapok[$yearmonth[1]];
	
	require ("../templates/default/statMonth.template.php");
}

function rateComment($comment,$rate)
{
	global $db_host, $cookiedomain;
	global $log;
	$log->debug2("init session");
	$context = singletonloader :: getInstance("contextClass");
	$context->session = new sessionclass(isset ($_COOKIE["your_mommy"]) ? $_COOKIE["your_mommy"] : "", $_SERVER["REMOTE_ADDR"], "/", $db_host);
	
	$bf = singletonloader :: getInstance("blogfacade");
	echo $bf->rateComment($comment,$context->user->ID,$rate);
	
}
?>