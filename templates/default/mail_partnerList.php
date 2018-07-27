<? if(is_array($partners) and sizeof($partners))
{
	$keys=array_keys($partners);
	sort($keys);

	?>
<ul class=taglist>
<?
foreach($keys as $key)
{
	?>
	<li class='tagsize<?=getTagClass($partners[$key],$min,$max);?>'><a href=/mail/from/<?=$key;?>/ ><?=$key;?> (<?=$partners[$key];?>)</a></li>
	<?
}
?>
</ul>
	<?
}
?>