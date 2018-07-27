<?
if(is_array($newFavourites) and sizeof($newFavourites))
{
?>
<h2>Változott könyvjelzők</h2>
<ul class="favourites">
<?php
foreach($newFavourites as $f)
{
?>
<li class="newFavourite"><a href="/users/<?=$f["diaryLogin"];?>/m_<?=$f["ID"];?>/" ><?=$f["title"];?>...</a> (<?=$f["userLogin"];?>, <?=$f["lastComment"];?>)</a></li>
<?
}
?>
</ul>
<?
}
?>

<?
if(is_array($favourites) and sizeof($favourites))
{
?>
<h2>Változatlan könyvjelzők</h2>
<ul class="favourites">
<?php
foreach($favourites as $f)
{
?>
<li class="newFavourite"><a href="/users/<?=$f["diaryLogin"];?>/m_<?=$f["ID"];?>/" ><?=$f["title"];?>...</a> (<?=$f["userLogin"];?>, <?=$f["lastComment"];?>)</a></li>
<?
}
?>
</ul>
<?
}
?>