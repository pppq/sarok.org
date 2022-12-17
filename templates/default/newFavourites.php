<?
if(count($favourites) > 0)
{
?>
<h3><a href="/favourites/" >Könyvjelzők</a></h3>
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
