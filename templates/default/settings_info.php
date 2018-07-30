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
<!--<img src="images/default/passports/einstein.jpg" title="beallitasok" style='padding-left: 10%'/>
onsubmit='if(editable.designMode=="on"") document.getElementById("innereditable_textarea").value=document.getElementById("innereditable").contentWindow.document.body.innerHTML;'

-->
<div class=banner>
<h1>Adataid</h1>
</div>

<form class='settings' id="editableForm" action="/settings/info/set/" method="post" onsubmit='isDirty=false;if(editable.designMode=="on") document.getElementById("innereditable_textarea").value=document.getElementById("innereditable").contentWindow.document.body.innerHTML'>
<fieldset>
<legend>Általanos adatok</legend>
<table>
<tr>
<td class='key'>Neved</td>
<td class='value'><input type=text name="name" value='<?=$name;?>'></td>
</tr>
<tr>
<td class='key'>Foglalkozásod</td>
<td class='value'><input type=text name="occupation" value='<?=$occupation;?>'></td>
</tr>
<tr>
<td class='key'>Nemed</td>
<td class='value'>
<?
$sexM=$sexF=$sexN="";
if($sex=='M') $sexM="checked";
elseif($sex=='F') $sexF="checked";
else $sexN="checked";
//echo $sexN;
?>
<input type=radio name="sex" <?=$sexM;?> value="M" class='checkbox' id='sexM'><label for='sexM'>Tudjátok, <b>férfi</b> vagyok</label><br />
<input type=radio name="sex" <?=$sexF;?> value="F" class='checkbox' id='sexF'><label for='sexF'>Hurrá, <b>nő</b> vagyok!</label><br />
<input type=radio name="sex" <?=$sexN;?> value="N" class='checkbox' id='sexN'><label for='sexN'>Nem tudom/nem mondom meg/nincs olyanom</label><br />
</td>
</tr>
<tr>
<td class='key'>Szemed szine</td>
<td class='value'><input type=text name="eyeColor" value='<?=$eyeColor;?>'></td>
</tr>
<tr>
<td class='key'>Hajad szine</td>
<td class='value'><input type=text name="hairColor" value='<?=$hairColor;?>'></td>
</tr>
<tr>
<td class='key'>Születésnapod</td>
<td class='value'><select name="birthYear" >
<?
if(strlen($birthYear)<4)
{
	$sel="selected";
}
	else $sel="";
echo "<option $sel value=''>-------</option>";
for($i=1921;$i<1995;$i++)
{
	if($i==$birthYear) $sel="selected";
	else $sel="";
	echo "<option $sel>$i</option>\n";
}

?>
</select>,
<?
$dates=split("/",$birthDate);
if(sizeof($dates)>1)
{
	$month=(int)$dates[0];
	$day=(int)$dates[1];
}
else
{
	$month=$day="";
}
?>
<select name=birthMonth>
<?
if(strlen($month)<1)
{
	$sel="selected";
}
	else $sel="";
echo "<option $sel value=''>----------------</option>";
for($i=1;$i<=12;$i++)
{
	if($i==$month) $sel="selected";
	else $sel="";
	echo "<option $sel value='$i'>".$honapok[$i]."</option>\n";
}
?>
</select>
 <input type=text name="birthDay" style='width: 2em' value='<?=$day;?>' size="4" maxlength="2">.
</td>
</tr>
<tr>
<td class='key'>Foglalkozásod</td>
<td class='value'><input type=text name="occupation" value='<?=$occupation;?>'></td>
</tr>
</table>
</fieldset>

<fieldset>
<legend>Bemutatkozásod</legend>
<table>
<tr>
<td class='key'>Kulcsszavak</td>
<td class='value'><input type=text name="keywords" value='<?=$keywords;?>'></td>
</tr>
<tr>
<td class='key' colspan=2>Pár szó magadrol</td>
</tr>
<tr>
<?
global $protocol, $gen_hostname;
$params="<base href='$protocol://$gen_hostname/'>" .
		"<link rel='stylesheet' type='text/css' href='/css/$skinName/entry.css'>";
?>
<td class='value' colspan=2 style='height: 10em;'><? putEditable("description",$description,$params);?></td>
</tr>
</table>
</fieldset>


<fieldset>
<legend>Földrajzi helyzet</legend>
<table>
<tr>
<td class='key'>Kerület</td>
<td class='value'><input type=text name="district" value='<?=$district;?>'></td>
</tr>
<tr>
<td class='key'>Város</td>
<td class='value'><input type=text name="city" value='<?=$city;?>'></td>
</tr>
<tr>
<td class='key'>Ország</td>
<td class='value'><input type=text name="country" value='<?=$country;?>'></td>
</tr>
</table>
</fieldset>

<fieldset>
<legend>Elérhetőség</legend>
<table>
<tr>
<td class='key'>E-mail cimed</td>
<td class='value'><input type=text name="email" value='<?=$email;?>'></td>
</tr>
<tr>
<td class='key'>Telefonod</td>
<td class='value'><input type=text name="phone" value='<?=$phone;?>'></td>
</tr>
<tr>
<td class='key'>ICQ</td>
<td class='value'><input type=text name="ICQ" value='<?=$ICQ;?>'></td>
</tr>
<tr>
<td class='key'>MSN</td>
<td class='value'><input type=text name="MSN" value='<?=$MSN;?>'></td>
</tr>
<tr>
<td class='key'>WIW</td>
<td class='value'><input type=text name="WIW" value='<?=$WIW;?>'></td>
</tr>
<tr>
<td class='key'>Skype</td>
<td class='value'><input type=text name="skype" value='<?=$skype;?>'></td>
</tr>
</table>
</fieldset>

<fieldset>
<legend>Titokzatosság</legend>
<table>
<tr>
<td class='key'>Ki láthatja ezeket az adatokat?</td>
<td class='value'>
<?
$publicA=$publicR=$publicF="";
if($publicInfo=='F' or $publicInfo=='N') $publicF="checked";
elseif($publicInfo=='R') $publicR="checked";
else $publicA="checked";
?>
<input type=radio name="publicInfo" <?=$publicA;?> value="A" class='checkbox' id='publicA'><label for='publicA'>Ezeket az adatokat <b>bárki</b> láthatja</label><br />
<input type=radio name="publicInfo" <?=$publicR;?> value="R" class='checkbox' id='publicR'><label for='publicR'>Ezeket az adatokat csak a <b>regisztrált tagok</b> láthatják</label><br />
<input type=radio name="publicInfo" <?=$publicF;?> value="F" class='checkbox' id='publicF'><label for='publicF'>Ezeket az adatokat csak a <b>barátaid</b> láthatják</label><br />
</td>
</tr>
</table>
</fieldset>
<button type=submit>Adatok mentése</button>
</form>

