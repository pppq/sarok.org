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
$honapok["10"]="Oktober";
$honapok["11"]="November";
$honapok["12"]="December";
?>
<div class=banner>
<h1>Egyéb dolgok</h1>
</div>
<script src=/javascript/dropdown.js></script>
<script>
function selectRadio(id)
{
	//alert(id);
	document.getElementById(id).checked="true";
}
</script>

<form class='settings' action="/settings/import/" method="post" enctype="multipart/form-data" onsubmit="this.style.visibility='hidden'">
<fieldset>
<legend>Import</legend>
Ha van blogod, ezzel a dologgal itt tudsz beimportalni ebbe.
<table>
<tr>
<td class='key'>Export forras</td>
<td class='value'>
<input type=file name=importfile >
</td>
</tr>
<tr>
<td class='key'>Mi ez</td>
<td class='value'>
<input type=radio name=importType value="sarok" checked id='import_sarok' class=checkbox ><label for='import_sarok' >Régi mentése a sarkos blogomnak</label><br />
<input type=radio name=importType value="freeblog" id='import_freeblog' class=checkbox disabled=true><label for='import_freeblog' >Ez egy freeblogos mentés (.freeblog.zip van a végén)</label><br />


</td>
</tr>
</table>
</fieldset>
<button type=submit>Megnézem a listát</button>
</form>

<form class='settings' action="/settings/magic/" method="post">
<fieldset>
<legend>Varázslatok</legend>
Ezzel a csodaeszközzel tudsz áthelyezni, megváltoztatni, menteni es törölni a bejegyzéseidet vagy azoknak egy részet.
<table>
<tr>
<td class='value'>
Az osszes <table style="width:auto;display: inline;vertical-align: middle;margin-right: 1em" >
<tr><td><input type=checkbox name=input_all id=input_all class='checkbox' value='y' checked><label for="input_all">publikus</label></td></tr>
<tr><td><input type=checkbox name=input_registered id=input_registered class='checkbox' value='y' checked><label for="input_registered">csak regisztraltaknak szolo</label></td></tr>
<tr><td><input type=checkbox name=input_friends id=input_friends class='checkbox' value='y' checked><label for="input_friends">csak baratoknak szolo</label></td></tr>
<tr><td><input type=checkbox name=input_private id=input_private class='checkbox' value='y'checked><label for="input_private">privat</label></td></tr>
</table> bejegyzest, <br/> ami <input type=text name=datefrom style='width: 8em' title="Formátum: ÉÉÉÉ-HH-NN">-tol es <input type=text name=dateto style='width: 8em' title="Formátum: ÉÉÉÉ-HH-NN">-ig volt megirva,<br />
tartalmazza a <input type=text name=input_search style='width: 40%' > kifejezést,
<br/> es a kovetkezo cimkeket tartalmazza: <input type=text name="tags" id='tags' style='width: 40%' onfocus=dropdownInit(event,this,"getTags") autocomplete=off>:<br /><br />
<input type=radio name=action value='delete' class='checkbox' id="action_delete"><label for=action_delete>torlom</label><br />
<input type=radio name=action value='changeaccess' class='checkbox' id="action_changeaccess"><label for=action_changeaccess>beteszem </label><select name=access onfocus='selectRadio("action_changeaccess")'><option value='ALL'>publikusra</option><option value='REGISTERED'>csak regisztraltaknak</option><option value='FRIENDS'>csak baratoknak</option><option value='PRIVATE'>privatba</option></select><br />
<input type=radio name=action value='changereadaccess' class='checkbox' id="action_changereadaccess"><label for=action_changereadaccess>hozzaszolas irasat engedelyezem </label><select name=readaccess onfocus='selectRadio("action_changereadaccess")'><option value='ALL'>mindenkinek</option><option value='REGISTERED'>csak regisztraltaknak</option><option value='FRIENDS'>csak baratoknak</option><option value='PRIVATE'>csak magamnak</option></select><br />

<input type=radio name=action value='addtags' class='checkbox' id="action_addtags"><label for=action_addtags>hozzaadom a </label><input type=text   name="tags_toput" id='tags_toput' style='width: 50%' onfocus='selectRadio("action_addtags");dropdownInit(event,this,"getTags")' autocomplete=off> cimkeket<br />
<input type=radio name=action value='deltags' class='checkbox' id="action_deltags"><label for=action_deltags>torlom a </label><input type=text name="tags_todel" id='tags_todel' style='width: 50%' onfocus='selectRadio("action_deltags");dropdownInit(event,this,"getTags")' autocomplete=off> cimkeket<br />
<input type=radio name=action value='save' class='checkbox' id="action_save"><label for=action_save>elmentem magamnak a gepre</label><input type=checkbox value='Y' name=comments id='withcomments' onfocus='selectRadio("action_save")' class='checkbox'><label for="withcomments" onfocus='selectRadio("action_save")'>hozzaszolasokkal egyutt.</label><br />
<input type=radio name=action value='nothing' class='checkbox' checked id="action_nothing"><label for=action_nothing>Nem csinalom veluk semmit, csak megnezem a listat</label>
</td>
</tr>
</table>
</fieldset>
<button type=submit>Megnezem a listat</button>
</form>

<form class='settings' action="/settings/other/set/" method="post">
<fieldset>
<legend>RSS forrás</legend>
<table>
<tr>
<td class='key'>RSS forrás cime</td>
<td class='value'><input type=text name="rss" value='<?=$rss;?>' id="rss_id" onblur="updateAData('checkRss',this.value,'rss_info')"><br/>
<label for=rss_id id=rss_info>Ha van már egy blogod valahol egy másik helyen, és van neki RSS-je, akkor ide beirhatod az RSS cimét és onnantol kezdve az ott lévő új bejegyzések megjelennek ebben a naplóban is. <br />
Ha nem tudod, mi az a RSS, akkor inkább hagyd űresen ezt a mezőt.</label>
</td>
</tr>
</table>
</fieldset>

<fieldset>
<legend>Böngészés</legend>
<table>
<tr>
<td class='key'>Hozzászolások:</td>
<td class='value'>
<?
$toMainPageN=$toMainPageY="";
if($toMainPage=='Y') $toMainPageY="checked";
else
$toMainPageN="checked";
?>
<input type=radio name="toMainPage" <?=$toMainPageN;?> value="N" class='checkbox' id='toMainPageN'><label for='toMainPageN'>Hozzászolás megírása után <b>ugyanarra az oldalra</b> ugorjon</label><br />
<input type=radio name="toMainPage" <?=$toMainPageY;?> value="Y" class='checkbox' id='toMainPageY'><label for='toMainPageY'>Ugorjon inkább a <b>főlapra</b> </label>
</td>
</tr>
<tr>
<td class='key'>WYSIWYG szerkesztő:</td>
<td class='value'>
<?
if(!isset($wysiwyg)) $wysiwyg="";
?>
<input type=checkbox name="wysiwyg" <?=$wysiwyg=='N'?"checked":"";?> value="N" class='checkbox' id='wysiwygLabel'><label for='wysiwygLabel'>Nem kell nekem wysiwyg szerkesztő, legyen normális.</label><br />
</td>
</tr>
<tr>
<td class='key'>Fölapon:</td>
<td class='value'>
<?
$friendListOnlyN=$friendListOnlyY="";
if($friendListOnly=='Y') $friendListOnlyY="checked";
else
$friendListOnlyN="checked";
?>
<input type=radio name="friendListOnly" <?=$friendListOnlyN;?> value="N" class='checkbox' id='friendListOnlyN'><label for='friendListOnlyN'><b>Mindenki</b> naplójába irt hozzászolásokat listázzon </label><br />
<input type=radio name="friendListOnly" <?=$friendListOnlyY;?> value="Y" class='checkbox' id='friendListOnlyY'><label for='friendListOnlyY'>Csak a <b>barátaim</b> naplójaiba irt hozzászolásokat listázzon</label>
</td>
</tr>
</table>
</fieldset>
<button type=submit>Adatok mentése</button>
</form>

<form class='settings' action="/settings/other/set/" method="post">
<fieldset>
<legend>Jelszó</legend>
<table>
<tr>
<td class='key'>Új jelszavad</td>
<td class='value'>
<input type=password name="pass" />
</td>
</tr>
<tr>
<td class='key'>Új jelszavad még egyszer:</td>
<td class='value'>
<input type=password name="pass2" />
</td>
</tr>
<tr>
<td colspan="2" class='value'>Ha el akarod érni rss-ben a minden bejegyzést, amit belépve láthatsz, biggyezd be ezt a kódot az URL végéhez: <br />
<input type=text readonly value="<?=$secret; ?>" /><br/>
Pl: <a href='/users/<?=$login;?>/friends/rss/<?=$secret;?>'>Barátaid bejegyzései rssben</a>.</td></tr>
</table>
</fieldset>
<button type=submit>Jelszó változtatása</button>
</form>


