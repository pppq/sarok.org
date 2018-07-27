<?php
if(is_array($item) and sizeof($item)>1){

 extract($item);
 ?>
<div class=comment id="c_<?=$ID;?>">
<div class='blog_info'><a href=/users/<?=$diaryLogin;?>/m_<?=$entryID;?>/#a_<?=$ID;?>  name=a_<?=$ID;?> class='anchor' title='<?=$ID;?>'>#<?=$ID;?></a>, <span class=date> <?=$createDate;?></span>,
<?
if($userID!="1"){
?>
<a href="/users/<?=$userLogin;?>/"><?=$userLogin;?></a>
<?}
else
 echo $userLogin;?>

<?if($canDelete)
{
?>
<a href='javascript:void(0)' onclick='javascript:updateAData("removeComment",<?=$ID;?>,"c_<?=$ID;?>")' >Törlés</a>
<!--<a href=/users/<?=$diaryLogin;?>/m_<?=$entryID;?>/delete/<?=$ID;?>/ id="delete_entry_button" title="hozzászolás törlése"><img src=/images/blog/cross.gif title="hozzászolás törlése" /></a> -->
<?}
?></div>
<? if($entry["access"]=="ALL" or $entry["access"]=="REGISTERED") {?>
<div class=rate><div class=rateVal id=rate_<?=$ID;?> ><?=$rate;?></div>
<? if($canRate) {?>
<span id=ratesux_<?=$ID;?> onclick="rateComment('<?=$ID;?>','sux')" title="Ez rossz!">-</span><span id=raterulez_<?=$ID;?> onclick="rateComment('<?=$ID;?>','rulez')" title="Ez jó!">+</span>
<? } ?>
</div>
<? } ?>
<div class=body>
<?=stripslashes($body);?>
</div>
</div>

<?} ?>

