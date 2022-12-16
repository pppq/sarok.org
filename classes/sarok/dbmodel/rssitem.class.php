<?php
class rssItem {
public $version;
public $encoding;
public $creator;
public $date;
public $items;
public $title;
public $decription;
public $link;
private $log;

//private $currentElements, $newsArray, $itemCount;

    function rssItem() {
		$this->log = singletonloader :: getInstance("log");
		$this->log->debug("rssItem initialized");
    }

public function readXML($xmlFile)
   {
   	global $feedObj, $newsArray,$itemCount, $currentElements;
   	$itemCount=0;
	$currentElements = array();
	$newsArray = array();

   	$feedObj=$this;
   	$this->log->debug("rssItem:reading $xmlFile");
     $xmlParser = xml_parser_create();

     xml_parser_set_option($xmlParser, XML_OPTION_CASE_FOLDING, TRUE);
     xml_set_element_handler($xmlParser, 'rss_startElement', 'rss_endElement');
     xml_set_character_data_handler($xmlParser, 'rss_characterData');


	/*if(!ereg("http://",$xmlFile))
	 return 4;*/
	$data=file_get_contents($xmlFile);
	$data=preg_replace("/.*<\?xml /","<?xml ",$data);
	$this->log->debug("read ".strlen($data)." bytes from file");
	//echo $data;
    $this->log->debug("parsing file");
    if(!xml_parse($xmlParser, $data))
     {
     	$this->log->error("XML parsing error at line #".xml_get_current_line_number($xmlParser).": ".xml_error_string(xml_get_error_code($xmlParser)));
     }
	else
	{
		$this->log->debug("XML parsing succesfull");
	}
    $this->log->debug("parse successfull");
     xml_parser_free($xmlParser);


	$item=array();
	foreach($newsArray as $a)
	{
	//print_r($a);
		//$this->items[$i]["description"]=strip_tags($this->items[$i]["description"]);
		$item["title"]=$a["title"];
		$item["link"]=$a["link"];
		$item["description"]=$a["description"];
		if(isset($a["dc:date"]))
		{
			$item["date"]=$a["dc:date"];
		}
		elseif(isset($a["pubdate"]))
		{
				$item["date"]=$a["pubdate"];
		}
		else
		{
			$this->log->error("no date (dc:date or pubdate specified!)");
			//return 2;
		}

		$item["sysdate"]=strtotime($item["date"]);
		$item["date"]=date("Y-m-d H:i:s",strtotime($item["date"]));
		if(!strlen($item["date"]))
		{
		$this->log->error("could not convert date!");
			//return 2;
		}

		if(isset($a["dc:subject"]))
		{
			$item["category"]=$a["dc:subject"];
		}
		elseif(isset($a["category"]))
		{
			$item["category"]=$a["category"];
		}
		else
		{
			$item["category"]="";
		}

		$this->items[]=$item;
	}
   if(sizeof($this->items)) return 0;
   else return 1;
   }


}


   // Sets the current XML element, and pushes itself onto the element hierarchy
   function rss_startElement($parser, $name, $attrs)
   {

     global $currentElements, $itemCount;
	//print_r($name);
//	//print_r($attrs);
     array_push($currentElements, $name);
		//echo $name;
     if($name == "ITEM"){$itemCount += 1;}

   }

   // Prints XML data; finds highlights and links
   function rss_characterData($parser, $data)
   {

     global $currentElements, $newsArray, $itemCount, $feedObj;

     $currentCount = count($currentElements);
     if($currentCount>1)
     	$parentElement = $currentElements[$currentCount-2];
     else $parentElement="";
     $thisElement = $currentElements[$currentCount-1];
  //   echo "newsArray[".($itemCount-1)."][".strtolower($thisElement)."] = $data;\n<br>\n<br>";

     if($parentElement == "ITEM"){

			if(isset($newsArray[$itemCount-1][strtolower($thisElement)]))
         		$newsArray[$itemCount-1][strtolower($thisElement)].= $data;
          else
          		$newsArray[$itemCount-1][strtolower($thisElement)]= $data;
		//echo   strtolower($thisElement).": ".$newsArray[$itemCount-1][strtolower($thisElement)]."<br />\n";
	//	echo   strtolower($thisElement).": ".$data."<br />\n";
        }
     elseif($parentElement == "CHANNEL"){
		switch($thisElement){
           case "TITLE":
              $feedObj->title.=$data;
               break;
           case "LINK":
           	$feedObj->link.=$data;
               break;
           case "DESCRIPTION":
           //echo "hey!";
              $feedObj->description.=$data;
               break;
           case "DC:CONTENT":
           //echo "hey!";
              $feedObj->description.=$data;
               break;
           case "DC:CREATOR":
               $feedObj->creator.=$data;
               break;
           case "CREATOR":
               $feedObj->creator.=$data;
               break;
               }
     }
     else{
		 switch($thisElement){
           case "title":
               break;
           case "link":
               break;
           case "description":
               break;
           case "language":
               break;
           case "item":
               break;
               }

		}

   }

   // If the XML element has ended, it is poped off the hierarchy
   function rss_endElement($parser, $name)
   {

     global $currentElements;

     $currentCount = count($currentElements);
     if($currentElements[$currentCount-1] == $name){
         array_pop($currentElements);}

   }


?>