<?php

class setAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->log->debug("Running setAction");
		if(!isset($_POST) or !sizeof($_POST) or sizeof($this->context->params)<2) return false;
		$data=$_POST;
		$target=$this->context->params[0];
		$this->log->debug("target is $target");
		switch($target)
		{
			case "info":
					$this->setInfo($data);
					break;
			case "blog":
					$this->setBlog($data);
					break;
			case "friends":
					$this->setFriends($data);
					break;
			case "skin":
					$this->setSkin($data);
					break;
			case "images":
					$this->setImages($data,$_FILES["file"]);
					break;
			case "map":
					$this->setMap($data);
					break;
			case "other":
					$this->setOther($data);
					break;
			default:
				$out=$this->setInfo($data);
				$target="info";
		}

	    //$settings=

		if(!isset($_POST["location"]))
		{
			$out["location"]="/settings/".$target."/";
		}
		else
		{
			$out["location"]=$_POST["location"];
		}
		return $out;
 	}

private function setInfo($data)
{
	$names=array('birthYear','city','country','district','email','eyeColor','hairColor',
'ICQ','MSN','skype','name','keywords','occupation','phone','publicInfo','sex','state','WIW');

	extract($data);
	$user=$this->context->user;
	$user->birthDate=$birthMonth."/".$birthDay;
	$this->log->debug("birthDate is ".$birthMonth."/".$birthDay);
	$user->description=addslashes($description);
	$user->description=$description;
	foreach($names as $value)
	{
		$this->log->debug("setting $value as ".$$value);
		$user->$value=$$value;
	}
	$user->commit();
	//$user->

}

private function setBlog($data)
{
	$names=array('blogAccess','blogName','commentAccess','copyright','google','messageAccess','statistics','entriesPerPage');

	extract($data);
	$user=$this->context->user;
//	$user->blogText=addslashes($blogText);
	$user->blogText=$blogText;
	$user->copyrightText=addslashes($copyrightText);
	foreach($names as $value)
	{
		$this->log->debug("setting $value as ".$$value);
		$user->$value=$$value;
	}
	$blogfacade=singletonloader::getInstance("blogfacade");
	$blogfacade->unlinkBlog($user->ID);
	$user->commit();
	//$user->

}

private function setFriends($data)
{
	extract($data);
	//print_r($data);
	$dirtylist=array();
	$user=$this->context->user;
	$this->log->debug("friends: ".implode(", ",$friends));
	$this->log->debug("bans: ".implode(", ",$bans));
	$this->log->debug("newFriend: $newFriend");
	$this->log->debug("newBan: $newBan");
	if(strlen($newFriend))
	{
		//add new friend;
		$newUser=$this->context->getUser($newFriend);
		$this->context->user->friends=array_merge($this->context->user->friends,array($newUser->ID));
		$this->log->debug("new Friends: ".implode(", ",$this->context->user->friends));
		$dirtylist[]=$newFriend;
	}

	if(strlen($newBan))
	{
		//add new friend;
		$newUser=$this->context->getUser($newBan);
		$this->context->user->bans=array_merge($this->context->user->bans,array($newUser->ID));
		$dirtylist[]=$newBan;
	}
	if(sizeof($friends))
		$this->context->user->friends=array_diff($this->context->user->friends,$friends);
	if(sizeof($bans))
		$this->context->user->bans=array_diff($this->context->user->bans,$bans);

	$this->log->debug("Committing user");
	$user->commit();

	foreach($friends as $value)
	{
		$dirtylist[]=$value;

	}
	foreach($bans as $value)
	{
		$dirtylist[]=$value;
		$dirtylist[]=$this->context->user->ID;
	}
	$dirtylist=array_unique($dirtylist);
if(sizeof($dirtylist))
{
$this->log->debug("DirtyList: ".implode(", ",$dirtylist));
	$sessionFacade=singletonloader::getInstance("sessionfacade");
	$db=singletonloader::getInstance("mysql");

	foreach($dirtylist as $ID)
	{
	$num=$db->querynum("select count(*) from sessions where userID='$ID' and activationDate>now() - interval 1 hour");
	if($num)   //this is the first login to the system
	{
	  $u=$this->context->getUser($ID);	
	  $u->commentsLoaded="0";
	  $u->entriesLoaded="0";
	  $u->commentsOfEntriesLoaded="0";
	  $u->myCommentsLoaded="0";
	  $u->commit();
	}
	}
}
	//$user->

}

private function setOther($data)
{
	extract($data);
	$user=$this->context->user;
	if(isset($toMainPage))
	{
		$user->toMainPage=$toMainPage;
		$user->friendListOnly=$friendListOnly;
	}
	if(isset($wysiwyg))
	{
		$user->wysiwyg="N";
	}
	else
	{
		$user->wysiwyg="Y";
	}
	if(isset($rss))
	{
		$this->log->debug("RSS is $rss");
		if($user->rss!=$rss)
		{
			$user->rss=$rss;
			
		
			$rssItem = singletonloader :: getInstance("rssItem");
			$val = $rssItem->readXML(trim($rss));
			if ($val == 0) {
				$this->log->debug("RSS is valid, updating it");
				$rssFacade=singletonloader::getInstance("rssfacade");
				$rssFacade->deleteFeed($this->context->user->ID);
				$rssFacade->addFeed($rss,$this->context->user->ID);
				$rssFacade->updateFeeds();
			}
		}
		else
		{
			$this->log->debug("Feeds are the same, performing no update");
		}
		
	}
	if(isset($pass) and $pass==$pass2 and strlen($pass)>3)
	{
		$user->pass=$pass;
	}
	$user->commit();
}

private function setSkin($data)
{
	extract($data);
	$user=$this->context->user;
	if(isset($css))
	{
		$user->css=$css;
		$user->friendListOnly=$friendListOnly;
	}
	if(isset($skinName))
	{
		$user->skinName=$skinName;
		$user->friendListOnly=$friendListOnly;
	}
	$user->commit();
}

private function setImages($data,$image)
{
	$im=singletonloader::getInstance("imagefacade");
	$filename=$image["name"];
	$location=$image["tmp_name"];
	$error=$image["error"];
	$this->log->debug("setImages: $filename, $location, $error");
	if($error==0)
	{
	$im->uploadImage($filename,$location,$this->context->user->ID);
	}
	else
	{
		$this->log->error("setImages: error occured");
	}
}

private function setMap($data)
{
	extract($data);
	$user=$this->context->user;
	if(isset($bindToMap) and $bindToMap=='Y')
	{
		$user->bindToMap="Y";
	}
	else
	{
		$user->bindToMap="N";
	}
	
	if(isset($deleteFromMap) and $deleteFromMap=='Y')
	{
		$user->posX="";
		$user->posY="";
	}
	else{
		if(is_numeric($posX) && is_numeric($posY))
		{
			$this->log->debug("posX is $posX && posY is $posY");
			$user->posX=$posX;
			$user->posY=$posY;
		}
	}
	$user->commit();
}
}
