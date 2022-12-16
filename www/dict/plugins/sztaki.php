<?php
$w=$_GET["w"];
$action=$_GET["action"];
$url="http://dict.sztaki.hu/dict_search.php?orig_lang=ENG%3AHUN%3AEngHunDict&orig_mode=3&W=$w";

if($action=="save")
{
$action="save";
}
else
{
$action="get";
}

echo $action($w);

function get($word)
{
global $url;
$text=file_get_contents($url);
//return($text);
$startSign="ltam:";
$endSign="</span>";
$pos=strpos($text,$startSign)+strlen($startSign);
$text=substr($text,$pos,strlen($text)-$pos);
$pos=strpos($text,$endSign);
$text=substr($text,0,$pos);
$items=explode("<!--m-->",$text);
$maxSize=sizeof($items);
$maxSize=min(5,$maxSize);
$out="";
for($i=1;$i<$maxSize;$i++)
{
$out.=$items[$i];	
}
$out=iconv("ISO-8859-2","UTF-8",$out);
return($out);
}


function save($word){
}
?>