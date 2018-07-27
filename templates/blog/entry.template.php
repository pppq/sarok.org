<?php
if(is_array($entry) and sizeof($entry)>1){

 extract($entry);
 ?>
<div class=entry id="m_<?=$ID;?>">
<h3><?=stripslashes($title);?> </h3>
<?
if($canChange)
{
?>
<a href=/users/<?=$diaryLogin;?>/m_<?=$ID;?>/edit/ class="edit_entry_button" title="bejegyzés szerkesztése"><img src=/images/blog/pencil.gif title="bejegyzés szerkesztése" /></a>
<a href=/users/<?=$diaryLogin;?>/m_<?=$ID;?>/delete/ class="delete_entry_button" title="bejegyzés törlése"><img src=/images/blog/cross.gif title="bejegyzés törlése" /></a>
<?}?>
<div class='info_<?=$access;?>' title='<?=$access;?>'>
<?
if(!isset($k))
{
	$dates=explode(" ",$createDate);
	$k=strtr($dates[0],"-","/");
}
?>
<span class=date><a href=/users/<?=$diaryLogin;?>/<?=$k;?>/  class="anchor"><?=$createDate;?></a></span>,
<?
if($diaryID==$userID)
{
?><a href="/users/<?=$diaryLogin;?>/info/"><?=$diaryLogin;?></a><?
}
else
{
?>
<a href="/users/<?=$userLogin;?>/info"><?=$userLogin;?></a> irta <a href="/users/<?=$diaryLogin;?>/"><?=$diaryLogin;?></a> naplójába<?
}
?>
</div>
<?
if(strlen($rssURL))
{
	?>
	<div class=rssMsg>Ez a  bejegyzés közvetitett. Az eredeti cime: <a href=<?=$rssURL;?> ><?=$rssURL;?></a></div>
	<?
}
?><div class="entryBody">
<?
echo stripslashes($body);
if($inBlog && strlen($body2)>2)
{
?><br /><a href="/users/<?=$diaryLogin;?>/m_<?=$ID;?>/#body2" >(Tovább...)</a><br /><?
}
else
{
	?><a name="body2" ></a><?
	echo stripslashes($body2);
}
?>
</div>
<?
if(isset($tags[$ID]) and sizeof($tags[$ID])){
?>
<div class="tags">Kulcsszavak: <?
foreach($tags[$ID] as $tag){
echo "<a href=/users/$userLogin/tags/$tag/ >$tag</a> ";
}
?>
</div>
<?
}

if($posX!=0 && $posY!=0)
{
?>
<div class="tags">Google Map: <a href=/users/<?=$diaryLogin;?>/m_<?=$ID;?>/map/ ><?=$posX; ?>, <?=$posY;?></a>
</div>
<?
}

if($inBlog)
{
?>
<div class="comments"><a href="/users/<?=$diaryLogin;?>/m_<?=$ID;?>/"><?=$numComments;?> hozzaszolas</a>
</div>
<? }
else
{?>
<div class="comments"><?
if($canHaveFavourite==true)
{
if($isFavourite)
	{
	?><a href=/favourites/del/<?=$entryID;?>/ onclick='javascript:updateAData("setFavourite",<?=$ID;?>,"fav");return false;' id="fav"><img src=/images/unbookmark.gif border=0 title="törlés a könyvjelzökből" ></a><?
	}
	else
	{
	?><a href=/favourites/add/<?=$entryID;?>/ onclick='javascript:updateAData("setFavourite",<?=$ID;?>,"fav");return false;' id="fav"><img src=/images/bookmark.gif border=0 title="könyvjelzökbe" ></a><?
	}
}
?> <a href="/users/<?=$diaryLogin;?>/m_<?=$ID;?>/#bottom"> le a tetejére</a> </div><?
}
?>
</div>
<?} ?>

