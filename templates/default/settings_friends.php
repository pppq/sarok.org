<?
global $honapok;
?>
<div class=banner>
<script src=/javascript/dropdown.js></script>
<h1>Barátok</h1>
</div>

<form class='settings' action="/settings/friends/set/" method="post" onsubmit='document.getElementById("innereditable_textarea").value=document.getElementById("innereditable").contentWindow.document.body.innerHTML;'>
<fieldset>
<legend>Barátok (<?=sizeof($friends); ?>)</legend>
Baratok olyan lenyek, akik lathatjak a 'csak baratoknak' bejelolt bejegyzeseidet. Semmi mas. Attol meg nem lesznek igazi barataid. Sajnalom. <br/>
sajat magadat is bejelolheted baratnak, de attol nem valtozik semmi, esetleg meditalsz rajta egy kicsit.<br/>
Vastaggal azok vannak bejelolve, akik viszajeloltek baratnak.
<table>
<tr>
<td class='key'>Felveszem ezt a tagot barátnak:</td>
<td class='value'><input type=text name="newFriend" onfocus=dropdownInit(event,this,"getUsers") autocomplete=off /><button type=submit>--&gt;</button></td>
</tr>
</table>
</fieldset>

<fieldset>
<table>
<tr>
<?
if(sizeof($friends)!=0)
{
for($i=0;$i<sizeof($friends);$i++)
{
	if($i%4==0) echo "</tr>\n<tr>";
	if(in_array($friends[$i],$friendOfs))
	{
		?>
		<td><input type='checkbox' name='friends[]' value='<?=$friends[$i];?>'><b><a href='/users/<?=$friendLogins[$friends[$i]];?>/'><?=$friendLogins[$friends[$i]];?></a></b></td>
	<?
	}else
{
	?>
		<td><input type='checkbox' name='friends[]' value='<?=$friends[$i];?>'><a href='/users/<?=$friendLogins[$friends[$i]];?>/'><?=$friendLogins[$friends[$i]];?></a></td>
	<?
}
}
}
else
{
	?>
		<td class='key'><i>Nincsenek barátaid</i></td>
	<?
}

?>
</tr>
</table>
<button type=submit>Törlöm a kijelölteket a barátaimbol</button>
</fieldset>

<fieldset>
<legend>Tiltottak (<?=sizeof($bans); ?>)</legend>
Tiltottak olyan regisztralt lenyek, amelyek nem latjak es nem szolhatnak hozza a naplodhoz. Ellenben Te sem latod es nem szolhatsz be nekik. Sot, meg a hozzaszulasokat sem latod. Akkor jo, ha valaki idegesit vagy zaklat.
Baratokat nem tehetsz tiltora, eloszor szedd ki oket a baratokbol.<br />
Ha valakit tobb mint 10 ember tett be tiltora, akkor az illetot havonta megkeressuk es elverjuk. Mi ezt ugy nevezzuk, hogy "kozossegepito trening".
<table>
<tr>
<td class='key'>Felveszem ezt a tagot a tiltottakhoz:</td>
<td class='value'><input type=text name="newBan" onfocus=dropdownInit(event,this,"getUsers") autocomplete=off /> <button type=submit>--&gt;</button></td>
</tr>

</table>
<table>
<tr>
<?
if(sizeof($bans)!=0)
{
for($i=0;$i<sizeof($bans);$i++)
{
	if($i%4==0) echo "</tr>\n<tr>";
	if(in_array($bans[$i],$banOfs))
	{
		?>
		<td><input type='checkbox' name='bans[]' value='<?=$bans[$i];?>'><b><a href='/users/<?=$banLogins[$bans[$i]];?>/'><?=$banLogins[$bans[$i]];?></a></b></td>
	<?
	}else
{
	?>
		<td><input type='checkbox' name='bans[]' value='<?=$bans[$i];?>'><a href='/users/<?=$banLogins[$bans[$i]];?>/'><?=$banLogins[$bans[$i]];?></a></td>
	<?
}
}
}
else
{
	?>
		<td class='key'><i>Nincsenek tiltottjaid</i></td>
	<?
}

?>
</tr>
</table>
<button type=submit>Törlöm a kijelölteket a tiltottakbol</button>
</fieldset>
</form>
