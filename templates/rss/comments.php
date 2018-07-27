<?
if(sizeof($entry)==0) return;

if(is_array($commentList) and sizeof($commentList)>0)
{
$commentList=array_reverse($commentList);
foreach($commentList as $item)
{
	require("comment.template.php");
}
}
?>
