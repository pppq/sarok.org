<?php
if(is_array($entries))
$dates=splitByDates($entries,"createDate");
if(isset($dates) && is_array($dates))
foreach($dates as $key=>$entries)
{
	$k=strtr($key,"-","/");

if(isset($entries) && is_array($entries))
foreach($entries as $entry)
{
	if(strtotime($key." ".$entry["createDate"])<=time())
	{
 		require("entry.template.php");
	}
}
}
?>

