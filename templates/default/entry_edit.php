<?
if(isset($entry))
{
extract($entry);
?>
<style type="text/css">
<!--
.sidebar{
	display:none;

}
.main{
	width: 95% !important;
}
-->
</style>
<script>
function selectRadio(id)
{
	//alert(id);
	document.getElementById(id).checked="true";
}
</script>
<div class="entry" id="editEntry">
<script>backup=true</script>
<script src=/javascript/dropdown.js></script>
<form name=editableForm action=/users/<?=$blogLogin;?>/m_<?=$ID;?>/update/ class=entry method=post onsubmit='isDirty=false;if(editable.designMode=="on") document.getElementById("innereditable_textarea").value=document.getElementById("innereditable").contentWindow.document.body.innerHTML'>
<input type=hidden name=ID value="<?=$ID;?>" />
<input type=hidden name=referrer value="<?=$_SERVER['HTTP_REFERER'];?>" />

<h3>Cime: <input type=text value="<?=$title;?>" name="title"  class='h3'></h3>
<div class='info_<?=strtoupper($messageAccess);?>' title='<?=$messageAccess;?>'>
<span class=date><span id=backup>nem volt még mentés</span></span>,
<a href="/users/<?=$userLogin;?>/"><?=$userLogin;?></a> irt a <input type=text name='diaryLogin' value='<?=$blogLogin;?>' size=30 maxlength=30 autocomplete="off" onfocus=dropdownInit(event,this,"getAvailableBlogs") > naplójába
</div>

  <?
global $gen_hostname;
$params="<base href='http://$gen_hostname/'>" .
		"<link rel='stylesheet' type='text/css' href='/css/$skinName/entry.css'>";
  	putEditable("body",stripslashes($body),$params,"entry");
  ?>
  <button type=submit accesskey='s'>Mehet</button>
  <fieldset>
<legend>Cimkék</legend>
<table>
<tr>
<td class='value'>
<input type=text name="tags" value='<?=implode(", ",$tags);?>' id='tags' style='width: 90%' onfocus=dropdownInit(event,this,"getTags") autocomplete=off>
</td>
</tr>
</table>

</fieldset>
  <fieldset>
<legend>Jogok</legend>
<table>
<tr>
<td class='key'>Ki láthatja a bejegyzést:</td>
<td class='value'>
<?
$messageAccess=strtolower($messageAccess);
$messageAccessR=$messageAccessF=$messageAccessP=$messageAccessA=$messageAccessL="";
if($messageAccess=='friends')
$messageAccessF='checked';
elseif($messageAccess=='registered')
$messageAccessR='checked';
elseif($messageAccess=='private')
$messageAccessP='checked';
elseif($messageAccess=='list')
$messageAccessL='checked';
else $messageAccessA='checked';
?>
<input type=radio name="access" <?=$messageAccessA;?> value='ALL' id='messageAccessA' class='checkbox'><label for='messageAccessA'><b>Mindenki</b> olvashatja</label><br/>
<input type=radio name="access" <?=$messageAccessR;?> value='REGISTERED' id='messageAccessR' class='checkbox'><label for='messageAccessR'>Csak a <b>regisztrált tagok</b></label><br/>
<input type=radio name="access" <?=$messageAccessF;?> value='FRIENDS' id='messageAccessF' class='checkbox'><label for='messageAccessF'>Csak a <b>barátaim</b></label><br/>
<input type=radio name="access" <?=$messageAccessP;?> value='PRIVATE' id='messageAccessP' class='checkbox'><label for='messageAccessP'>Senki, <b>csak én</b></label><br/>
<input type=radio name="access" <?=$messageAccessL;?> value='LIST' id='messageAccessL' class='checkbox'><label for='messageAccessL'>Senki, <b>csak ezek a tagok:</b><br/></label><input type=text name=list style='width:100%' value='<?=$list;?>'  onfocus='selectRadio("messageAccessL");dropdownInit(event,this,"getUserList")' autocomplete=off >
</td>
</tr>
<tr>
<td class='key'>Ki szólhat hozzá:</td>
<td class='value'>
<?
$commentAccess=strtolower($comments);
$commentAccessR=$commentAccessF=$commentAccessP=$commentAccessA="";
if($commentAccess=='friends')
$commentAccessF='checked';
elseif($commentAccess=='registered')
$commentAccessR='checked';
elseif($commentAccess=='private')
$commentAccessP='checked';
else $commentAccessA='checked';
?>
<input type=radio name="comments" <?=$commentAccessA;?> value='ALL' id='commentAccessA' class='checkbox'><label for='commentAccessA'><b>Mindenki</b></label><br/>
<input type=radio name="comments" <?=$commentAccessR;?> value='REGISTERED' id='commentAccessR' class='checkbox'><label for='commentAccessR'>Csak a <b>regisztrált tagok</b></label><br/>
<input type=radio name="comments" <?=$commentAccessF;?> value='FRIENDS' id='commentAccessF' class='checkbox'><label for='commentAccessF'>Csak a <b>barátaim</b></label><br/>
<input type=radio name="comments" <?=$commentAccessP;?> value='PRIVATE' id='commentAccessP' class='checkbox'><label for='commentAccessP'>Senki, <b>csak én</b></label>
</td>
</tr>
</table>
</fieldset>

<fieldset>
<table class="settings">
<tr>
<td class=value colspan=2 >
<input type=checkbox class=checkbox id=needsMap name=needsMap value=Y <?if($needsMap) echo "checked"; ?> ><label for=needsMap>Hozzáadok egy térképet<label>
</td>
</tr>
<span id="map_area">
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
</span>
</table>
<?global $gmap_key; ?>
<script src="http://maps.google.com/maps?file=api&v=2&key=<?=$gmap_key;?>"
 type="text/javascript" ></script>
 <script src="/javascript/gmap.js"
 type="text/javascript" ></script>
<script>
gmap=true;
<?
if(is_numeric($posX) && is_numeric($posY) && ($posX*$posY!=0))
{
	?>
	initP=new GLatLng(<?=$posY;?>,<?=$posX;?>);
	
	showMarker=true;
	<?
}
?>
</script>

 <button type=submit accesskey='s'>Mehet</button>
</form>
</div>
<?}?>
