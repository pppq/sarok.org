<div class=blog>
<h1><?=$blogName;?></h1>
<?php
if(sizeof($entry)==0) return;
$inBlog=false;
require("entry.template.php");
?>
<?
if(is_array($commentList) and sizeof($commentList)>0)
{
	?>
<div class=comments>
<h3><?=sizeof($commentList);?> hozzászolás</h3>
<?
foreach($commentList as $item)
{
	require("comment.template.php");
}
?><a name=bottom></a>
</div>
<?
}
if($canCommentIt)
{

global $gen_hostname;
$params="<base href='http://$gen_hostname/'>" .
		"<link rel='stylesheet' type='text/css' href='/css/$skinName/entry.css'>";
	?>
	<div class=comments>
<h3>Mondj már valamit, <?=$myLoginName;?>!</h3>
	<form name=editableForm class=comment action=/users/<?=$diaryLogin;?>/m_<?=$entryID;?>/insertcomment/ method=post >
<?if(!$isLoggedIn)
{
	?>
	Neved: <input type=text name=your_name accessKet='e' value='<?=$your_name;?>' ><br />
	&nbsp;&nbsp;Web: <input type=text name=your_web value='<?=$your_web;?>' ><br />
	<?
}
?>
  <?
  	putEditable("body","",$params,"comment");
  ?>
  <input type=submit value=Mehet accessKey='s'>
	</form>
	</div>
	<?
}
?>
</div>
