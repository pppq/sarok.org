<?
$honapok["1"]="Január";
$honapok["2"]="Február";
$honapok["3"]="Március";
$honapok["4"]="Április";
$honapok["5"]="Májús";
$honapok["6"]="Június";
$honapok["7"]="Július";
$honapok["8"]="Augusztus";
$honapok["9"]="Szeptember";
$honapok["10"]="October";
$honapok["11"]="November";
$honapok["12"]="December";
?>
<div class=banner>
<h1>Kulső</h1>
</div>

<form class='settings' action="/settings/skin/set/" method="post">
<fieldset>
<legend>Nézetek <?=$skinName; ?></legend>
<table>
<? foreach($skins as $name=>$title)
{ ?>
<tr>
<td class='key' ><?=$title;?></td>

<td class='value'>
<? if($skinName==$name) 
{
	$selected="checked";
}
else
{
	$selected="";
}
?>
<input type="radio" name=skinName value="<?=$name;?>" id="id_<?=$name;?>" <?=$selected; ?> ><label for='id_<?=$name;?>'><img src=/images/skins/<?=$name;?>.jpg /> </label>
</td>
</tr>

<? } ?>
</table>
</fieldset>
<fieldset>
<legend>CSS</legend>
<table>
<tr>
<td class='key'>Kiegészitő css:</td>
</tr>
<tr>
<td class='value'>
<textarea name=css style='height:35em' ><?=$css?></textarea>
</td>
</tr>


</table>
</fieldset>
<button type=submit>Adatok mentése</button>
</form>



