<?php
class blogAP extends ActionPage {
public $queryString;
public $params;
public $entryCode,$rows,$tags,$numComments,$month, $year;

	/*
	 * Types of blog url-s:
	 * /m_xxxx/   --comments
	 *
	 *
	 * /search/				--search form
	 * /search/keyword/		--entries
	 *
	 * /yyyy/   -archive
	 *
	 * /
	 * /yyyy/mm/			--entries
	 * /yyyy/mm/dd/
	 *
	 * /friends
	 * /friends/yyyy/mm/
	 * /friends/yyyy/mm/dd/
	 * /friends/yyyy/mm/dd/search/keyword/skip/20/
	 *
	 *
	 * /new
	 * /insertcomment/
	 *
	 *  -delete, change
	 * /map/
	 *
	 */

	function init() {
		parent :: init();
		global $skinName;
		$this->log->debug2("Initializing blogAP, params: ".implode(", ", $this->context->params));
		$bf=singletonloader::getInstance("blogfacade");
		$mysql=singletonloader::getInstance("mysql");
		$sf=singletonloader::getInstance("sessionfacade");
		
		$user = $this->context->user;
		$blog = $this->context->blog;
		if($user->ID==1 and strlen($blog->skinName)) $skinName=$blog->skinName;
		$ID=$user->ID;
		$skipnum=$blog->entriesPerPage;
		if(!is_numeric($skipnum)) $skipnum="10";
		$p = $this->context->params;
		if($p[sizeof($p)-1]=="rss" or $p[sizeof($p)-2]=="rss")
		{
			$this->templateName="rss";
			$this->log->debug("Will be rss");
			if($p[sizeof($p)-2]=="rss")
			{
				$secret=$p[sizeof($p)-1];
				$this->log->info("Trying auth via rss secret code $secret!");
				
				$tempUser=$sf->extractUserID($secret);
				if($tempUser>1)
				{
				$this->log->info("Using auth via rss secret code $secret!");	
					$this->context->user=$this->context->requestUserDAL($tempUser);
					$user=$this->context->user;
				}
				unset($p[sizeof($p)-1]);
			}
			unset($p[sizeof($p)-1]);
			
		}
		else
		{
			$this->templateName="blog";
		}
		$action = "entry";
		$skip=0;
		$entryCode = $friends = $search = $keyword = $year = $month = $day = $tags= $tagword = "";
		// Analyzing Entry
			$params=$bf->analyzePath($p,$user,$blog);
			$this->params=$params;
		if (sizeof($p)>0 and substr($p[0], 0, 2) == "m_") {
			$this->entryCode = substr($p[0], 2, strlen($p[0]) - 2);
			$this->log->debug("Displaying entry #$entryCode");
			$action="comments";
			if(sizeof($p)>1 and ($p[1]=="update" or $p[1]=="edit" or $p[1]=="delete" or $p[1]=="insertcomment") )
			{
				$action="entry_".$p[1];
			}

		}elseif(sizeof($p)>0 and ($p[0] == "new" or $p[0]=="update" or $p[0]=="info"))
		{
			$action="entry_".$p[0];
		}

		// Analyzing friends
		else {
			$action="entry";


			extract($params);
		}

		if($p[sizeof($p)-1]=="map")
		{
			unset($p[sizeof($p)-1]);
			$action="blogMap";
			$this->templateName="blog";
		}

		if($action=="entry")
		{
			$friends_crit=$date_crit=$search_crit=$skip_crit=$tags_crit="TRUE";
		if($blog->login!='all')
		{
		if($friends===true)
		{
			$friendL=$blog->friends;
				if(sizeof($friendL))
				{
					$friends_crit=" e.diaryID in (".implode(", ",$friendL).")";
				}
				else
				{
					$friends_crit=" false";
				}
			//$friends_crit="e.diaryID in (select userID from friends where friendOf='".$blog->ID."' and friendType='friend')";
		}
		else
		{
			$friends_crit="e.diaryID ='".$blog->ID."'";
		}
		}

		if(is_numeric($year) and is_numeric($month))
		{
			$date_crit="date_format(e.dayDate,'%Y-%c')='$year-$month'";
			if(is_numeric($day))
			{
				$date_crit="dayDate='$year-$month-$day'";
			}
			$this->year=$year;
			$this->month=$month;
		}
		else
		{
		
			//$date_crit="createDate>=now() - interval 6 month";
		}
		/*if($this->templateName=='rss')
		{
			$date_crit.=" and dayDate<now() ";
		}*/
		if($tags===TRUE)
		{
			$tagword=strip_tags($tagword);
			$tags_crit="( e.ID in (select entryID from categories where Name='$tagword'))";

		}

		if($search===TRUE)
		{
			$keyword=strip_tags($keyword);
			$search_crit="(e.title like '%$keyword%' or e.body like '%$keyword%' or e.body2 like '%$keyword%')";
		}

		/*$bans=$user->bans;
		$bans=array_merge($bans,$user->banOfs);
		$this->log->debug("bans merged, number of bans is: ".sizeof($bans));
		$banstr="";
		if(sizeof($bans))
		{
			$bans=array_unique($bans);
			for($i=0;$i<sizeof($bans);$i++) $bans[$i]="'".$bans[$i]."'";
			$banstr="and e.userID not in(".implode(",",$bans).") ";

			$this->log->debug("adding banned criteria into the string: $banstr");
		}*/
		$banstr="";  //The banned users are handled in the entries

		$grants=$bf->genBlogQuery($friends,$user,$blog);


		$q="SELECT  ID, diaryID, userID, createDate, modifyDate, access, title, body, body2, numComments, moderatorComment, category, posX,posY, rssURL  from entries as e  where
		$friends_crit AND\n $date_crit AND \n$search_crit AND \n$tags_crit AND\n
		 $grants
		$banstr
		order by e.createDate desc LIMIT $skip, $skipnum";

		$this->queryString=$q;
		//$this->log->debug("query string is $q");
		$loadedFromCache=false;
		if($bf->isMainPage($params))
		{
			$this->log->debug("loading main page of the blog");
			$fType=$bf->getUserType($user,$blog);
			$fileName=$_SERVER["DOCUMENT_ROOT"]."/../cache/blogs/d{$blog->ID}-$fType";
			if(file_exists($fileName))
			{
				$this->log->debug("$fileName exists, unserializing data");
				$s = implode("", @file($fileName));
				$this->rows=unserialize($s);
				$loadedFromCache=true;
			}
			else
			{
				$this->log->debug("$fileName does not exists, serializing data");
				$this->rows=$mysql->queryall($q);
				$canSerialize=true;
				foreach($this->rows as $r) if($r["access"]=="LIST") {$canSerialize=false; break;}
				if($fType=="self") $canSerialize=true;
				if($canSerialize)
				{
					$s = serialize($this->rows);
	  				$fp = fopen($fileName, "w");
	  				fwrite($fp, $s);
	  				fclose($fp);
	  				$this->log->debug("serialization successfull");
				}
				else
				{
					$this->log->debug("serialization unsuccessfull");
				}
			}
		}
		else
		{
		$this->rows=$mysql->queryall($q);
		}

		if(sizeof($this->rows))
		{
		//print_r($this->rows);
			$codes=array();
			for($i=0;$i<sizeof($this->rows);$i++){
				$codes[]=$this->rows[$i]["ID"];
			}

				$q="select * from categories where entryID in (".implode(", ",$codes).")";
				$tagrows=$mysql->queryall($q);
				$tags=array();
				for($i=0;$i<sizeof($tagrows);$i++)
				{
					$tags[$tagrows[$i]["entryID"]][]=$tagrows[$i]["Name"];
				}
				$this->log->debug("Number of tags is ".sizeof($tags));
				$this->tags=$tags;
				if($loadedFromCache)
				{
					$this->log->debug("Getting proper numComments for the cached entries");
					$q="select ID, numComments from entries where ID in (".implode(", ",$codes).")";
					$numComRows=$mysql->queryall($q);
					for($i=0;$i<sizeof($numComRows);$i++)
					{
						$numComments[$numComRows[$i]["ID"]]=$numComRows[$i]["numComments"];
					}
					for($i=0;$i<sizeof($this->rows);$i++) $this->rows[$i]["numComments"]=$numComments[$this->rows[$i]["ID"]];
				}

		}
		}

		/*$this->actionList["logout"][]="logoutform";*/
		/*$this->actionList["newmail"][]="checkMail";*/
		/*$this->actionList["menu"][]="menu";
		$this->actionList["leftMenu"][]="leftMenu";*/
		$this->log->debug("Main action is $action");
		$this->actionList["main"][] = $action;
		$this->actionList["calendar"][] = "month";
		$this->actionList["navigation"][] = "navigation";
		$this->actionList["sidebar"][] = "sidebar";
		$this->actionList["header"][] = "header";
		$this->actionList["header"][] = "customCss";
		//$this->actionList["friendlist"][]="logoutForm";

		return $this->actionList;
	}

	public function canRun() {
		//return $this->context->props["blog"];
		return true;
	}
}
?>
