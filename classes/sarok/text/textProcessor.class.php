<?
class textProcessor{
	private $text;
	private $log;
	function textProcessor()
	{
		$this->log=singletonloader::getInstance("log");
	}

	public function getText()
	{
		return $this->text;
	}
	public function setText($text)
	{
		$this->text=$text;
	}

	public function preFormat($str="")
	{
		$this->log->debug2("Preformatting $str");
		
	if(!strlen($str)) $str=$this->text;
	//return($str);
	$allowed_tags="<a><h1><h2><h3><center><i><b><span><div><ul><li><small><big><sup><sub><strike><u><input><select><button><pre><hr><blockquote><code><img><object><param><embed><nowrap><select><option><form><iframe><br>";
	//$str=strip_tags($str,$allowed_tags);
	$str=stripslashes($str);
//	$this->log->debug2("Stripslashesh $str");
	$str=str_replace("!!!!!!!!!!!!!!!","Idióta vagyok! Valaki lőjön le engem!",$str);
	$str=str_replace("???????????????","Idióta vagyok? Valaki lőjön le engem!",$str);

	$expr="Tegnap megismerkedtem egy fiúval és azóta csak rá gondolok. Gyönyörű göndör haja van, kék szeme és vastag farka, amely alig fér be a se... Bocs, azt hiszem rossz ablakba írtam!";
	$str=eregi_replace(" lol ",$expr,$str);
//$this->log->debug2("Replace idiots $str");
	$str=ereg_replace("(<br/>)*!!![! \n\r]+","!!!",$str);
	$str=ereg_replace("(<br/>)*\?\?\?[\? \n\r]+","???",$str);
	$str=ereg_replace("(<br/>)*\?!\?![\?! \n]+","?!?!",$str);
//	$str=preg_replace("/([^ ]+)\\1{7,}/","\\1",$str); 
//	$this->log->debug2("Replace multiline $str");

	$str=str_replace("(c)","&copy;",$str);
	$str=str_replace("(r)","&reg;",$str);
	$str=str_replace("(tm)","&#153;",$str);
	//$str=$this->tidy($str);
//$this->log->debug2("Replace copy $str");
	$str=$this->cleanUp($str);
	//$this->log->debug2("Done preformatting $str");	
	return($str);
}

public function postFormat($str="")
{
	global $search_keyword;
	$this->log->debug2("Postformatting");
	if(!strlen($str)) $str=$this->text;
	$str=str_replace(" -- "," &ndash; ",$str);
	$str=str_replace(",-- "," &ndash; ",$str);
	$str=str_replace(" --,"," &ndash; ",$str);
	$str=ereg_replace("uid_([A-Za-z0-9]+)","<a href=/users/\\1/ class=personid>\\1</a>",$str);
	if(isset($search_keyword) && strlen($search_keyword)>0) $str=str_ireplace($search_keyword,"<span class='search'>$search_keyword</span>",$str);
	return($str);
}

public function tidy($text)
{
	global $tidy_cmd, $tidy_cmd_xhtml, $xhtmlMode;
	if(!isset($xhtmlMode) or $xhtmlMode)
	{
		$cmd=$tidy_cmd_xhtml;
	}
	else
	{
		$cmd=$tidy_cmd;
	}
	//$cmd=$tidy_cmd;

	$this->log->debug("textProcessor->tidy()");
	if(!sizeof($text)) $text=$this->text;
	   $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
           1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
           2 => array("pipe", "a") // stderr is a file to write to
                             );
    $this->log->debug("proc_opening $cmd");
    $pipes=array();
    $process = proc_open($cmd, $descriptorspec, $pipes);
	$this->log->debug("The result of proc_open is: $process");
    fwrite($pipes[0], $text);
    fclose($pipes[0]);
	$this->log->debug("closed input");
	$stro="";
       while (!feof($pipes[1])) {
           $stra=fgets($pipes[1], 1024);
				//	 $this->log->debug("Out: ".$stra);
					 $stro.=$stra;
       }
       fclose($pipes[1]);
       $return_value = proc_close($process);
       $this->log->debug("Return value is $return_value");
       $this->log->debug("Output is ".strlen($stro)." bytes long");
       $this->log->debug("Output is: ".$stro);
       return($stro);
}

public function cleanUp($text="")
{
	if($text=="") $text=$this->text;
	$this->log->debug("cleanup() -->");
	$this->log->debug("tidying");
//	$text=$this->tidy($text);
	$this->log->debug("replacing EOL-s");
	//$text=str_replace("\n","",$text);
	//$text=str_replace("\r","",$text);
	//$this->log->debug("Output is ".$text);
	$this->log->debug("removing headers, leaving only body content");
	$text=ereg_replace(".*<body>","",$text);
	$text=ereg_replace("</body>.*","",$text);
	//$text=ereg_replace(".*<body>(.*)</body>.*","\\1",$text);   <-- this is very fucking slow!
	$this->log->debug("cleanup() <--");
	return($text);
}

}
?>
