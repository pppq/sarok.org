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
<h1>Blog</h1>
</div>

<form class='settings' action="/settings/blog/set/" method="post" onsubmit='document.getElementById("innereditable_textarea").value=document.getElementById("innereditable").contentWindow.document.body.innerHTML;'>
<fieldset>
<legend>Naplóleirások</legend>
<table>
<tr>
<td class='key'>Blogod neve</td>
<td class='value'><input type=text name="blogName" value='<?=$blogName;?>'></td>
</tr>
<tr>
<td class='key'>Bejegyzések száma egy oldalon</td>
<td class='value'><input type=text name="entriesPerPage" value='<?=$entriesPerPage;?>'  style='width: 2em' size="4" maxlength="2"></td>
</tr>
<tr>
<td class='key'>Ki irhat a naplódba:</td>
<td class='value'>
<?
$blogAccessR=$blogAccessF=$blogAccessP="";
if($blogAccess=='friends')
$blogAccessF='checked';
elseif($blogAccess=='registered')
$blogAccessR='checked';
else $blogAccessP='checked';
?>
<input type=radio name="blogAccess" <?=$blogAccessR;?> value='registered' id='blogAccessR' class='checkbox'><label for='blogAccessR'><b>Regisztrált</b> tagok</label><br/>
<input type=radio name="blogAccess" <?=$blogAccessF;?> value='friends' id='blogAccessF' class='checkbox'><label for='blogAccessF'>Csak a <b>barátaim</b></label><br/>
<input type=radio name="blogAccess" <?=$blogAccessP;?> value='private' id='blogAccessP' class='checkbox'><label for='blogAccessP'>Senki, <b>csak én</b></label>
</td>
</tr>
<tr>
<td class='key' colspan=2>Napló utmutató, blablabla szöveg, megjelenik jobboldalt a naplótol:</td>
</tr>
<tr>
<?
global $protocol, $gen_hostname;
$params="<base href='$protocol://$gen_hostname/'>" .
		"<link rel='stylesheet' type='text/css' href='/css/$skinName/entry.css'>";
?>
<td class='value' colspan=2 style='height: 10em;'><? putEditable("blogText",$blogText,$params);?></td>
</tr>
</table>
</fieldset>

<fieldset>
<legend>Alapértelmezések</legend>
<table>
<tr>
<td class='key' colspan=2><b>Figyelem:</b> ezek csak alapértelmezett beállitások, nem terjednek ki a már megirt bejegyzésekre, bejegyzés iráskor megváltoztathatod!</td>
</tr>
<tr>
<td class='key'>Ki láthatja az új bejegyzést:</td>
<td class='value'>
<?
$messageAccessR=$messageAccessF=$messageAccessP=$messageAccessA="";
if($messageAccess=='friends')
$messageAccessF='checked';
elseif($messageAccess=='registered')
$messageAccessR='checked';
elseif($messageAccess=='private')
$messageAccessP='checked';
else $messageAccessA='checked';
?>
<input type=radio name="messageAccess" <?=$messageAccessA;?> value='all' id='messageAccessA' class='checkbox'><label for='messageAccessA'><b>Mindenki</b> olvashatja</label><br/>
<input type=radio name="messageAccess" <?=$messageAccessR;?> value='registered' id='messageAccessR' class='checkbox'><label for='messageAccessR'>Csak a <b>regisztrált tagok</b></label><br/>
<input type=radio name="messageAccess" <?=$messageAccessF;?> value='friends' id='messageAccessF' class='checkbox'><label for='messageAccessF'>Csak a <b>barátaim</b></label><br/>
<input type=radio name="messageAccess" <?=$messageAccessP;?> value='private' id='messageAccessP' class='checkbox'><label for='messageAccessP'>Senki, <b>csak én</b></label>
</td>
</tr>
<tr>
<td class='key'>Ki szólhat hozzá:</td>
<td class='value'>
<?
$commentAccessR=$commentAccessF=$commentAccessP=$commentAccessA="";
if($commentAccess=='friends')
$commentAccessF='checked';
elseif($commentAccess=='registered')
$commentAccessR='checked';
elseif($commentAccess=='private')
$commentAccessP='checked';
else $commentAccessA='checked';
?>
<input type=radio name="commentAccess" <?=$commentAccessA;?> value='all' id='commentAccessA' class='checkbox'><label for='commentAccessA'><b>Mindenki</b></label><br/>
<input type=radio name="commentAccess" <?=$commentAccessR;?> value='registered' id='commentAccessR' class='checkbox'><label for='commentAccessR'>Csak a <b>regisztrált tagok</b></label><br/>
<input type=radio name="commentAccess" <?=$commentAccessF;?> value='friends' id='commentAccessF' class='checkbox'><label for='commentAccessF'>Csak a <b>barátaim</b></label><br/>
<input type=radio name="commentAccess" <?=$commentAccessP;?> value='private' id='commentAccessP' class='checkbox'><label for='commentAccessP'>Senki, <b>csak én</b></label>
</td>
</tr>
</table>
</fieldset>

<fieldset>
<legend>Szerzői jogok</legend>
<table>
<tr>
<?
$copyrightChecked="";
if($copyright=='Y') $copyrightChecked="checked";
?>
<td class='value' colspan=2><input type=checkbox name="copyright" value='Y' <?=$copyrightChecked;?> onclick="toggle('copyrightTextTr')" class='checkbox' id='copyrightChecked'><label for='copyrightChecked'>A naplóban lévő írások terjesztése a Creative Commons Attribution-NonCommercial-ShareAlike 1.0 licence szerint történik (Szabad felhasználni, terjeszteni, átdolgozni; Nem szabad kereskedelmi célokra használni, még az átdolgozást sem; Kötelező hivatkozni a szerzőre).</label></td>
</tr>
<tr <?=$copyright=='Y'?"style='display:none'":"style='display:block'"?> id='copyrightTextTr'>
<td class='key'>Amennyiben nem felel meg a Creative Commons License, ide ird be a sajat szerzöi jogi nyilatkozatot:
</td>
<td class='value'><textarea name='copyrightText'><?=$copyrightText?></textarea>
</td>
</tr>

</table>
</fieldset>

<fieldset  >
<legend>Egyeb</legend>
<table >
<tr>
<?
$googleChecked="";
if($google=='N') $googleChecked="checked";
?>
<td class='value' colspan=2><input disabled=true type=checkbox name="google" value='N' <?=$googleChecked;?> class='checkbox' id='googleChecked'><label for='googleChecked'>Nem akarom, hogy a google elvtárs és a kollégái tudjanak a naplómrol.
</label></td>
</tr>

<tr>
<?
$statisticsChecked="";
if($statistics=='N') $statisticsChecked="checked";
?>
<td class='value' colspan=2><input disabled=true type=checkbox name="statistics" value='N' <?=$statisticsChecked;?> class='checkbox' id='statisticsChecked'><label for='statisticsChecked'>Nem akarom, hogy a naplóm statisztikája nyilvános legyen.
</label></td>
</tr>
</table>
</fieldset>
<button type=submit>Adatok mentése</button>
</form>

