<?php
$id="yandex";
$w=$_GET["w"];
$action=$_GET["action"];
$url="http://lingvo.yandex.ru/en?text=$w&st_translate=1";

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
$startSign="<DIV LANG=ru>";
$endSign="</DIV>";
$pos=strpos($text,$startSign)+strlen($startSign);
$text=substr($text,$pos,strlen($text)-$pos);
$pos=strpos($text,$endSign);
$text=substr($text,0,$pos);
$text=strip_tags($text);

$text=str_replace("\n\n","<br />",$text);

return($text);
}


function save($word){
}
?>