<ul class="menu">
<?php
for($i=0;$i<sizeof($menu);$i++)
{
?>
<li><a href="<?=$menu[$i]["url"];?>"><?=$menu[$i]["name"];?></a></li>
<?
}
?>
</ul>