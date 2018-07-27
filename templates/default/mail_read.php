<?
if(isset($mail) and is_array($mail))
{
	extract($mail);
	$d=explode(" ",$date);
?>
<table class=mail id=mailBody >
<thead>
<tr>
<td class='key'>Kitől:</td>
<td class='value'><a href=/mail/from/<?=$senderLogin;?>/ ><?=$senderLogin;?></a></td>
</tr>
<tr>
<td class='key'>Kinek:</td>
<td class='value'><a href=/mail/from/<?=$senderLogin;?>/ ><?=$recipientLogin;?></a></td>
</tr>
<tr>
<td class='key'>Elküldve:</td>
<td class='value'><a href=/mail/<?=$d[0];?>/ ><?=$d[0];?></a> <?=$d[1];?></td>
</tr>
<tr>
<td colspan=2> <a href=/mail/<?=$ID;?>/reply/ >Válasz</a> <a href='javascript:void(0)' onclick='this.style.display="none";updateAData("removePrivate","<?=$ID;?>","mailBody")' >Törlés</a></td>
</tr>
</thead>
<tbody>
<tr>
<td colspan=2 class=body >
<h2><?=strlen(trim($title))?$title:"nincs is";?>
<? if($replyOn!=0)
{
?><br /><small><a href=/mail/<?=$replyOn;?> >Előzmény</a></small><? } ?></h2>

<?=stripslashes($body);?>
</td>
</tr>
<tr>
<td colspan=2> <a href=/mail/<?=$ID;?>/reply/ >Válasz</a> <a href='javascript:void(0)' onclick='this.style.display="none";updateAData("removePrivate","<?=$ID;?>","mailBody")' >Törlés</a> </td>
</tr>
</tbody>
</table>
<?
}
else {
 ?>

  <h2 class="error">Sajnalom, de ezt a levelet nem Neked szánták olvasásra</h2>

  <? } ?>