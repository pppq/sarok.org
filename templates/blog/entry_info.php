<h1><a href=/users/<?=$login;?>/ ><?=$login;?></a></h1>
<? global $honapok; ?>
<div class=infosheet id="<?=$userID;?>" >
<?
foreach($props as $prop)
{
	//echo "$prop: ".$$prop." <br />\n";
}
?>
<table><caption>Általános</caption>
<tr>
<td class="key">Név:</td>
<td class="value"><?=$name;?></td>
</tr>
<tr>
<td class="key">Regisztráció:</td>
<td class="value"><?=$createDate;?></td>
</tr>
<tr>
<td class="key">Útolsó aktivitás:</td>
<td class="value"><?=$activationDate;?></td>
</tr>
</table>

<table><caption>Adatok</caption>
<? if(strlen($occupation)) { ?>
<tr>
<td class="key">Foglalkozása:</td>
<td class="value"><?=$occupation;?></td>
</tr> <? } ?>
<? if(strlen($birthYear.$birthDate)) { ?>
<tr>
<td class="key">Szuletesnap:</td>
<? if(strlen($birthDate))
{
	$d=explode("/",$birthDate);
	$birthDay=$d[1];
	$birthMonth=$honapok[$d[0]];
}
?>

<? if(strlen($birthYear))
{
	$birthYear=", ".$birthYear;
}
?>
<td class="value"><?=$birthMonth;?> <?=$birthDay;?>. <?=$birthYear;?></td>
</tr> <? } ?>

<? if(strlen($sex) && $sex!="N") { ?>
	 <tr>
<td class="key">Neme:</td>
<?
if($sex=='F') $sex="Nő";
else $sex="Férfi";
?>
<td class="value"><?=$sex;?></td>
</tr> <? } ?>
<? if(strlen($eyeColor)) { ?>
	 <tr>
<td class="key">Szeme:</td>
<td class="value"><?=$eyeColor;?></td>
</tr> <? } ?>

<? if(strlen($hairColor)) { ?>
	 <tr>
<td class="key">Haja:</td>
<td class="value"><?=$hairColor;?></td>
</tr> <? } ?>
</table>

<table><caption>Kapcsolat</caption>
<? if(strlen($phone)) { ?>
<tr>
<td class="key">Telefon:</td>
<td class="value"><?=$phone;?></td>
</tr> <? } ?>

<? if(strlen($email)) { ?>
	 <tr>
<td class="key">Email:</td>
<td class="value"><a href=mailto:<?=$email;?> ><?=$email;?></a></td>
</tr>
<? } ?>

<? if(strlen($ICQ)) { ?>
	 <tr>
<td class="key">ICQ:</td>
<td class="value"><img style='display:none' src=http://web.icq.com/whitepages/online?icq=<?=$ICQ;?>&img=5><?=$ICQ;?></td>
</tr>
<? } ?>

<? if(strlen($WIW)) { ?>
	 <tr>
<td class="key">WIW:</td>
<td class="value"><?=$WIW;?></td>
</tr>
<? } ?>

<? if(strlen($MSN)) { ?>
	 <tr>
<td class="key">MSN:</td>
<td class="value"><?=$MSN;?></td>
</tr>
<? } ?>
<? if(strlen($skype)) { ?>
	 <tr>
<td class="key">Skype:</td>
<td class="value"><?=$skype;?></td>
</tr>
<? } ?>
</table>
<h2>Bemutatkozás:</h2>
<?=$description;?>

<table><caption>Barátok</caption>
 <tr>
<td class="key">Ezeket a tagok a barátai (<?=sizeof($friends);?>): </td>
<td class="value">
<?
$friendlist=array();
foreach($friends as $friend)
{
	$f=$logins[$friend];
	$item="<a href=/users/$f/ >$f</a>";
	if(in_array($friend,$myFriends))
	{
		 $item="<strong>$item</strong>";
	}
	$friendlist[]=$item;
}
echo implode(", ",$friendlist);
?>
</td>
</tr>
<tr>
<td class="key">Ezeknek a tagoknak a barátja (<?=sizeof($friendOfs);?>):</td>
<td class="value">
<?
//print_r($myFriends);
$friendlist=array();
foreach($friendOfs as $friend)
{
	$f=strtolower($logins[$friend]);
	$item="<a href=/users/$f/ >$f</a>";
	if(in_array($friend,$myFriends))
	{
		 $item="<a href=/users/$f/ ><strong>$f</strong></a>";
	}
	$friendlist[]=$item;
	sort($friendlist);
}
echo implode(", ",$friendlist);
?>
</td>
</tr>
</table>
</div>
