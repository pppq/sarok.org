<div class=banner>
<h1>Térkép</h1>
</div>
<form class='settings' action="/settings/map/set/" method="post">
<fieldset><legend></legend>
<table>
<tr>
<td class="value" colspan=2>
<?
if($bindToMap=='Y') $checked="checked"; else $checked=""; 
?>
<input type=checkbox class=checkbox id=bindToMap name=bindToMap value=Y <?=$checked;?> ><label for=bindToMap>A bejegyzéseim is tudjanak térképhez kötödni</label>
</td>

</tr>
<tr>
<td class="key">
Jelenlegi koordináták:
</td>
<td class=value>
<input type="text" name=posX id=posx onblur="updateMap()" value="<?=$posX;?>" > <input type="text" name=posY id=posy onblur="updateMap()"  value="<?=$posY;?>">
</td>
</tr>
<tr>
<td colspan=2>
<div id=mapHolder>
<div id="map"></div>
</div>
</td>
</tr>
<tr>
<td class=value colspan=2 >
<input type=checkbox class=checkbox id=delete name=deleteFromMap value=Y><label for=delete>Vagy törlöm magam a térképről</label>
</td>
</tr>
</table>
<input type=submit class=submit value="OK">
<?global $gmap_key; ?>
<script src="http://maps.google.com/maps?file=api&v=2&key=<?=$gmap_key;?>"
 type="text/javascript" ></script>
 <script src="/javascript/gmap.js"
 type="text/javascript" ></script>
<script>
gmap=true;
<?
if(is_numeric($posX) && is_numeric($posY))
{
	?>
	initP=new GPoint(<?=$posX;?>,<?=$posY;?>);
	GLevel=4;
	showMarker=true;
	<?
}
if(is_array($coords))
foreach($coords as $count=>$coord)
{
	?>
pList[<?=$count;?>]=new GMarker (new GPoint(<?=$coord["posX"]; ?> ,<?=$coord["posY"]; ?> ));
pList[<?=$count;?>].id=<?=$count;?>;
names[<?=$count;?>]="<?=$coord["login"]?>";
	<?
}

?>
</script>
<fieldset>
</form>