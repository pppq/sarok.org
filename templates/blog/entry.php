<div class=blog>
<h1><?=$blogName;?></h1>
<?php
$inBlog=true;
if(is_array($entries))
$dates=splitByDates($entries,"createDate");
if(isset($dates) && is_array($dates))
foreach($dates as $key=>$entries)
{
	$k=strtr($key,"-","/");
	?><h2><?=$k;?></h2><?
if(isset($entries) && is_array($entries))
foreach($entries as $entry)
{
 require("entry.template.php");
}
}
?>
</div>
