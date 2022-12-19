<?php
global $protocol, $gen_hostname;
extract($item);
if($access!="ALL")
{
	$title=$title." ($access)";
}
?>
<item>
      <title><?=$userLogin;?></title>
      <link><?=$protocol?>://<?=$gen_hostname?>/users/<?=$diaryLogin;?>/m_<?=$entryID;?>/#a_<?=$ID;?></link>
      <description><![CDATA[<?=stripslashes($body);?>]]></description>
      <dc:subject><?=$userLogin;?></dc:subject>
      <dc:date><?=date("r",strtotime($createDate));?></dc:date>
      <pubDate><?=date("r",strtotime($createDate));?></pubDate>
</item>
