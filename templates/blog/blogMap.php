<h1><?=$blogName;?> Térképe</h1>
<a href=/users/<?=$diaryLogin;?>/map/ >Az összes koordináta megjelenitése</a>  
<div class=settings>
<div id=mapHolder>
<div id="map"></div>
</div>
</div>
<?global $gmap_key; ?>
<script src="http://maps.google.com/maps?file=api&v=2&key=<?=$gmap_key;?>"
 type="text/javascript" ></script>
 <script src="/javascript/gmap.js"
 type="text/javascript" ></script>
<script>
gmap=true;
changePos=false;
<?
if(is_array($coords))
foreach($coords as $count=>$coord)
{
	?>
pList[<?=$count;?>]=new GMarker (new GLatLng(<?=$coord["posY"]; ?>,<?=$coord["posX"]; ?>  ));
pList[<?=$count;?>].id=<?=$count;?>;
text[<?=$count;?>]="<?=$coord["text"]?>";
	<?
}
if(sizeof($coords)==1)
{
	?>
	initP=new GLatLng(<?=$coord["posY"]; ?>,<?=$coord["posX"]; ?>);
	GLevel=13;
	showMarker=true;
	<?
}

?>

</script>
