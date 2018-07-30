<?extract($entry);
if($access!="ALL")
{
	$title=$title." ($access)";
}
$body=$body.$body2;
if($posX!=0 && $posY!=0)
{
$body.="<br /><br />Google Map: <a href=https://www.sarok.org/users/$diaryLogin/m_$ID/map/ >$posX; , $posY </a>";
}
?>
<item>
      <title><![CDATA[<?=$diaryLogin;?>: <?=stripslashes($title);?>]]></title>
      <link>https://www.sarok.org/users/<?=$diaryLogin;?>/m_<?=$ID;?>/</link>
      <description><![CDATA[<?=stripslashes($body);?>]]></description>
        <pubDate><?=date("r",strtotime($createDate));?></pubDate>
      <? if(isset($tags[$ID]) and sizeof($tags[$ID]))  echo "<dc:subject>".implode(", ",$tags[$ID])."</dc:subject>";  ?>
    
</item>

