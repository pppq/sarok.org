<ul class="menu">
<?php
$url="/".substr($url,0,strlen($url)-2)."/";
for($i=0;$i<sizeof($menu);$i++)
{
$selected="";
//echo $url;
//echo $url." ".$menu[$i]["url"];
//echo $_GET["p"];
if(isset($_GET["p"]))
	$p="/".$_GET["p"];
else $p="/";
//echo $p."<br/>";
//if(strpos($menu[$i]["url"],$p)!==FALSE and strlen($p)>1)
	//$selected="id='selected_item'";

?>
<li><a href="<?=$menu[$i]["url"];?>" <?=$selected;?>><?=$menu[$i]["name"];?></a></li>
<?
}
?>
</ul>
