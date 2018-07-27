<script>
var checkNewMail=true;
//alert(loadList);
<? global $refreshTime; ?>
function checkMail()
{
	//alert("huj");
	updateAData("checkMail","","newmail");
	setTimeout("checkMail()",1000*<?=$refreshTime;?>);
}

</script>
<?php
if($num>0)
{
	?>
<div class=checkMail >
<h4><?=$login;?>, van <a href=/mail/><b><?=$num;?></b> új leveled.</a></h4>
<img src="/images/brianmay.gif" style="float:left" />
<div class=lastMail> Legutolso level:
<a href="/mail/<?=$last["ID"]?>/"><?=strlen(trim($last["title"]))?$last["title"]:"Nincs tárgy";?></a>,<br /> kuldoje <a href=/users/<?=$last["senderLogin"]?>/><?=$last["senderLogin"]?></a>, <?=$last["date"]?></div>
<? if($num>1) { ?>
<div class=firstMail>Legelso level:
<a href="/mail/<?=$first["ID"]?>/"><?=strlen(trim($first["title"]))?$first["title"]:"Nincs tárgy";?></a>,<br /> kuldoje <a href=/users/<?=$first["senderLogin"]?>/><?=$first["senderLogin"]?></a>, <?=$first["date"]?></div>
<? } ?>
<hr style="visibility:none;clear:both">
</div>

	<?
}
?>
