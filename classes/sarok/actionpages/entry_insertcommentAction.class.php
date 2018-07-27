<?php
class entry_insertcommentAction extends Action{
protected $sessionFacade,$mysql;
 	function execute()
 	{
 		global $myCookies;
		if(!is_array($_POST) or sizeof($_POST)<1) return array();
		$blog=$this->context->blog;
		$user=$this->context->user;
		$out["blogLogin"]=$blog->login;
		$body=$_POST["body"];

		if(isset($_POST["your_name"]))
		{
			$signature=$_POST["your_name"];
			$myCookies["your_name"]=$_POST["your_name"];
			//setcookie("your_name",$_POST["your_name"],time()+3600*24*30);
			if(isset($_POST["your_web"]) && strlen($_POST["your_web"])>8)
			{
				$myCookies["your_web"]=$_POST["your_web"];
				//setcookie("your_web",$_POST["your_web"],time()+3600*24*30);
				if(strpos($_POST["your_web"],"@"))
				{
					$signature="<a href=mailto:".$_POST["your_web"]." >$signature</a>";
				}
				elseif(strpos(" ".$_POST["your_web"],"://"))
				{
					$signature="<a href='".$_POST["your_web"]."' >$signature</a>";
				}
				else
				{
					$signature="<a href='http://".$_POST["your_web"]."' >$signature</a>";
				}
			}
			$body.="<br /><br />$signature";

		}
		$bf=singletonloader::getInstance("blogfacade");
		$this->mysql=singletonloader::getInstance("mysql");
		//if($user->login=="Anonim") return array();
		$entryID=$this->context->ActionPage->entryCode;
		$entry=$this->mysql->queryone("select * from entries where ID='$entryID' and diaryID='".$blog->ID."'");
		if(!is_array($entry) or sizeof($entry)<1) return array();
		if(!$bf->canComment($entry,$user->ID)) return(array());
		//if($user->ID==775) $body="<i>".$body."</i>";
		$Code=$bf->addComment(0,$entryID,$user->ID,$this->format($body));
		if($user->toMainPage=='Y')
			$out["location"]="/";
		else
			$out["location"]="/users/".$blog->login."/m_".$this->context->ActionPage->entryCode."/";
		return $out;
 	}
 	
 	private function format($text) {
	global $log;
	//if(isset($_POST["Text"])) $text=$_POST["Text"];
	//str_replace("%26","&",$text);
	//$text=unescape($text);
	$log->debug2("Start");
	$tp = singletonloader :: getInstance("textProcessor");
	$log->debug2("preformatting");
	$text = $tp->preFormat($text);
	$log->debug2("postformatting");
	$text = $tp->postFormat($text);
	$log->debug2("cleaning up");
	$text = $tp->cleanUp($text);
	$log->debug2("Finished cleanup");
	return $text;
}
}
?>