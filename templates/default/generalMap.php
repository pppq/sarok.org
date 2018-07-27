<h1>Lefedettség</h1>
<input type="hidden" name=posX id=posx> <input type="hidden" name=posY id=posy >
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
if(is_numeric($posX) && is_numeric($posY))
{
	?>
	initP=new GLatLng(<?=$posY;?>,<?=$posX;?>);
	GLevel=13;
	showMarker=true;
	<?
}
if(is_array($coords))
foreach($coords as $count=>$coord)
{
	?>
pList[<?=$count;?>]=new GMarker (new GLatLng(<?=$coord["posY"]; ?>,<?=$coord["posX"]; ?>  ));
pList[<?=$count;?>].id=<?=$count;?>;
names[<?=$count;?>]="<?=$coord["login"]?>";
	<?
}

?>

</script>