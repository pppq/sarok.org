<?
class importfacade {
	private $dbcon;
	private $log;
	private $db,$df,$sf,$bf;
	private $context;
	public $query;
	const action_New=0;
	const action_UnDelete=1;
	const action_Modify=2;
	function importfacade() {
		$this->log = singletonloader :: getInstance("log");
		$this->db = singletonloader :: getInstance("mysql");
		$this->df = singletonloader :: getInstance("dbfacade");
		$this->sf = singletonloader :: getInstance("sessionfacade");
		$this->bf = singletonloader :: getInstance("blogfacade");
		$this->context = singletonloader :: getInstance("contextClass");

		$this->log->info("importfacade initialized");

	}


	public function setUserData($userID,$props)
	{
		$this->log->debug("Setting user data");
		$user=$this->context->requestUserDAL($userID);
		foreach($props as $name=>$value)
		{
			$user->$name=$value;
		}
		$user->commit();

	}

	public function getEntryCodes($userID)
	{
		$q="select ID from entries where diaryID='$userID'";
		$rows=$this->db->queryall($q);
		$codes=array();
		foreach($rows as $row)
		{
			$codes[]=$row["ID"];
		}
		return $codes;
	}

	public function getAction($entryID,$userID)
	{
		$this->log->debug2("Checking action for the entry $entryID and diary $diaryID");
		if($entryID=="" or $entryID==0)
		{
		 $this->log->debug("Entry does not exist");
		 return action_New;
		}
		$q="select count(*) as num from entries where ID='$entryID' and diaryID='$userID' and isTerminated='N'";
		$entryNum=$this->db->querynum($q);
		if($entryNum>0)
		{
			$this->log->debug("Entry exists");
			return action_Modify;
		}
		else
		{
			$q="select count(*) as num from entries where ID='$entryID' and diaryID='$userID' and isTerminated='Y'";
			$entryNum=$this->db->querynum($q);
			if($entryNum>0)
				{
					$this->log->debug("Entry exists");
					return action_UnDelete;
				}
			else {
					$this->log->debug("Entry does not exist in given diary");
					return action_New;
			}
		}
	}

	public function commitEntries($entries,$diaryID)
	{
		$this->log->debug("Committing ".sizeof($entries)." entries for $diaryID");
		foreach($entries as $entry)
		{
			if(isset($entry["action"]))
				$action=$entry["action"];
			else
				$action=$this->getAction($entry["ID"],$diaryID);
		$this->log->debug("action is $action");
			if(!($userID=$this->sf->getUserCode($entry["userID"])))
			{
				$userID=$diaryID;
			}
		$this->log->debug("userID is $userID");
			$entryID=$entry["ID"];

			switch($action)
			{
				case 'action_New':
						$this->log->debug("adding Entry");
						$entryID=$this->bf->addEntry(
						$diaryID,$userID,$entry["createDate"],$entry["access"],null,
						$entry["comments"],$entry["title"],$entry["body"],$entry["body2"],$entry["tagSet"]);

						break;
				case 'action_UnDelete':
						$this->log->debug("Undeleting Entry");
						$this->bf->unDeleteEntry($entry["ID"]);
				case 'action_Modify':
						$this->log->debug("modifying entry");
						$this->bf->changeEntry($entry["ID"],$diaryID,$entry["access"],null,
						$entry["comments"],$entry["title"],$entry["body"],$entry["body2"],$entry["tagSet"]);
						$entryID=$entry["ID"];
						break;
				default: $this->log->error("Undefined constant of action");
			}
			if(sizeof($entry["commentSet"]))
			{
				$this->log->debug("adding Comments");
				foreach($entry["commentSet"] as $comment)
					{
						if(!($userID=$this->sf->getUserCode($comment["userID"])))
							{
								$userID=$diaryID;
							}
							$this->log->debug("adding comment ".$comment["body"]);
						$this->bf->addComment(0,$entryID, $userID, $comment["body"],$comment["createDate"]);
					}
			}
		}

	}

public function getDiary($file,$userID)
{
	$this->log->debug("GetDiary($file, $userID)");
	$data=$this->getDiaryFromXML($file);
	return($this->postProcess($data,$userID));

}

public function postProcess($entries,$diaryID)
{
		foreach($entries as $index=>$entry)
		{
			if(!isset($entry["userID"])) {
				$entries[$index]["userID"] = $this->sf->getUserLogin($diaryID);
			}
			if(!isset($entry["diaryID"])) {
				$entries[$index]["diaryID"] = $this->sf->getUserLogin($diaryID);
			}
			if(!isset($entry["createDate"]))
			{
				$entries[$index]["createDate"]=now();
				$entry["createDate"]=now();
			}
			if(!ereg("[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}",$entry["createDate"]))
			{
				$entries[$index]["createDate"]=date("Y-m-d G:i:s",strtotime($entry["createDate"]));
			}
			$entries[$index]["action"]=$this->getAction($entry["ID"],$diaryID);
		}
		return $entries;
}

private function getDiaryFromXML($xmlFile){
	global $import_currentElements, $import_currentCount, $import_entries, $import_currentEntry,$import_currentComment;
	$this->log->debug("getDiaryFromXML($xmlFile)");
   	$import_currentCount=0;
   	$import_currentElements=array();
   	$import_entries=array();
	$this->log->debug("Creating XML parser");
     $xmlParser = xml_parser_create();

     xml_parser_set_option($xmlParser, XML_OPTION_CASE_FOLDING, FALSE);
     xml_set_element_handler($xmlParser, 'import_startElement', 'import_endElement');
     xml_set_character_data_handler($xmlParser, 'import_characterData');

	$this->log->debug("getting content of $xmlFile");
	$data=file_get_contents($xmlFile);
	$data=ereg_replace(".*<\?xml","<?xml",$data);
	//echo $data;
	$this->log->debug("Parsing XML");
     if(!xml_parse($xmlParser, $data))
     {
     	$this->log->error("XML parsing error at line #".xml_get_current_line_number($xmlParser).": ".xml_error_string(xml_get_error_code($xmlParser)));
     }
	else
	{
		$this->log->debug("XML parsing succesfull");
	}
     //print_r($import_entries);
     //echo "parsed!";

     xml_parser_free($xmlParser);
	$this->log->debug("XML parser freed");
   // Sets the current XML element, and pushes itself onto the element hierarchy
   return($import_entries);
	}
}

 function import_startElement($parser, $name, $attrs)
   {

     global $import_currentElements , $commentCount, $entryCount, $import_entries, $comments, $import_currentEntry,$import_currentComment,$datarow;

//	//print_r($attrs);
     array_push($import_currentElements, $name);

		//echo $name;
     if($name == "comment")
     {

     	$import_currentComment=array();
     }
     if($name == "entry"){
     	$import_currentEntry=array();
     	$import_currentEntry["tagSet"]=array();
     	$import_currentEntry["commentSet"]=array();

     	}


   }

   // Prints XML data; finds highlights and links
   function import_characterData($parser, $data)
   {

     global $import_currentElements, $import_currentEntry,$import_currentComment;

     $import_currentCount = count($import_currentElements);
     $parentElement="";
     if($import_currentCount>=2)
     	$parentElement = $import_currentElements[$import_currentCount-2];
     $thisElement = $import_currentElements[$import_currentCount-1];
    // echo "newsArray[".($itemCount-1)."][".strtolower($thisElement)."] = $data;\n<br>\n<br>";

		if($thisElement=="tag"){

     		array_push($import_currentEntry["tagSet"],$data);
		}
     	elseif($parentElement=="entry")
     	{

     		$import_currentEntry[$thisElement]=$data;
     	}
     	elseif($parentElement=="comment")
   		{

     		$import_currentComment[$thisElement]=$data;
   		}

	}

   // If the XML element has ended, it is poped off the hierarchy
   function import_endElement($parser, $name)
   {

     global $import_currentElements,$import_currentEntry,$import_currentComment,$import_entries;
	     $import_currentCount = count($import_currentElements);
/*     if($import_currentCount>=2)
     	$parentElement = $import_currentElements[$import_currentCount-2];*/
     $thisElement = $import_currentElements[$import_currentCount-1];
     $thisElement = $name;
     if($thisElement=="entry")
     		array_push($import_entries,$import_currentEntry);
     if($thisElement=="comment")
     		array_push($import_currentEntry["commentSet"],$import_currentComment);

     $import_currentCount = count($import_currentElements);

     if($import_currentElements[$import_currentCount-1] == $name){
         array_pop($import_currentElements);
         }

   }
?>