<?
error_reporting(0);

class rssfacade {
	private $dbcon;
	private $log;
	private $db,$df,$sf,$bf;
	private $context;
	public $query;
	const action_New=0;
	const action_UnDelete=1;
	const action_Modify=2;
	function rssfacade() {
		$this->log = singletonloader :: getInstance("log");
		$this->db = singletonloader :: getInstance("mysql");
		$this->bf = singletonloader :: getInstance("blogfacade");
		
		$this->context=singletonloader::getInstance("contextClass");
		$this->log->info("importfacade initialized");

	}

		public function getFeedList()
		{
			$this->log->debug("Getting list of feeds to update");
			$q="select * from feeds where status<>'banned' and nextUpdate<=now()";
			$rows=$this->db->queryall($q);
			return($rows);
		}

		public function getAllFeeds()
		{
			$this->log->debug("Getting list of all feeds");
			$q="select * from feeds where status<>'banned' order by feedUrl";
			$rows=$this->db->queryall($q);
			return($rows);
		}


		public function updateFeeds()
		{
			$this->log->debug("Updating feeds");
			//$feeds=$this->getFeedList();
			$feeds=$this->getAllFeeds();
			if(is_array($feeds) and sizeof($feeds))
			{
			foreach($feeds as $feed)
			{
				extract($feed);
				$lastUpdateDate=strtotime($lastUpdate);
				$now=time();
				$result=$this->updateFeed($feedURL,$lastUpdate,$lastEntry,$blogID);
				if($result["count"]<=1)
				{
					$nextUpdate=(int)ceil($now+($now-$lastUpdateDate));
				}
				else
				{

					$nextUpdate=(int)ceil($now+$result["avg"]/3);
				}
				$this->updateSchedule($ID,$nextUpdate,$result["lastEntry"]);
			}
			}
		}

		public function updateFeed($url,$lastUpdate,$lastEntry,$blogID)
		{
			$this->log->debug("Updating feed $url");
			$feed=new rssItem();
			$curDate=time();
			$this->log->debug("Current date is $curDate");
			$lastDate=strtotime($lastUpdate);
			$this->log->debug("lastUpdate $lastDate");
			$feed->readXML($url);
			$itemCount=0;
			$entries=array();
			$blogObj=$this->context->requestUserDAL($blogID);
			
			foreach($feed->items as $entry)
			{
				if($entry["link"]==$lastEntry)
				{
				 $this->log->debug("read $itemCount new entries");
				 break;
				}
				$itemCount++;
				$entries[]=$entry;
			}
			$i=0;
			$out["count"]=sizeof($entries);
			if(sizeof($entries))
			{
			$min=time();
			$max=0;
			$messageAccess=$blogObj->messageAccess;
			$commentAccess=$blogObj->commentAccess;
			if(!strlen($messageAccess)) $messageAccess="ALL";
			if(!strlen($commentAccess)) $commentAccess="ALL";
			foreach($entries as $entry)
			{
				if(!strlen($entry["sysdate"]) or $entry["sysdate"]<100)
				{
					$entry["sysdate"]=(int)ceil($curDate+$i*($curDate-$lastDate)/$itemCount);
				}
				$min=min($min,$entry["sysdate"]);
				$max=max($max,$entry["sysdate"]);
				//extract ($entry);
				$tags=array();
				if(strlen($entry["category"]))
				{
					$tags=split("[ ,;]+",$entry["category"]);
				}
				
				$this->bf->addEntry($blogID, $blogID, date("Y-m-d H:i:s",$entry["sysdate"]), strtoupper($messageAccess), array(),strtoupper($commentAccess), $entry["title"], $entry["description"],"", $tags,0,0,$entry["link"]);
				$i++;
			}
			$this->bf->unlinkBlog($blogID);
			$out["avg"]=($max-$min)/$out["count"];
			$out["lastEntry"]=$entries[0]["link"];
			}
			else
			{
				$out["lastEntry"]=$lastEntry;
			}
				return $out;
		}

			public function updateSchedule($feedID,$nextUpdate,$lastURL)
			{
				$nU=date("Y-m-d H:i:s",$nextUpdate);
				$this->log->debug("next update of the feed $feedID is on $nU");
				$q="update feeds set lastUpdate=now(), nextUpdate='$nU', lastEntry='$lastURL' where ID='$feedID' limit 1";
				$this->db->mquery($q);
			}

		public function addFeed($url,$blogID,$email="")
		{
			$this->log->debug("Adding $url to feeds");
		try{
			$q="insert into feeds (feedURL, blogID, lastUpdate, nextUpdate, contactEmail) values ('$url','$blogID', now() - interval 1 day, now(), '$email')";
			$this->db->mquery($q);
			$feedID=mysqli_insert_id();
			$this->log->debug("adding successfull, feed ID is $feedID");
			return($feedID);
		}
		catch(mysqlException $e)
		{
			$this->log->error("Adding failed");
			return false;
		}

		}
		
		public function deleteFeed($blogID)
		{
			$this->log->debug("Deleting feed for the $blogID");
			$q="delete from feeds where blogID='$blogID' limit 1";
			$this->db->mquery($q);
		}

   }
?>
