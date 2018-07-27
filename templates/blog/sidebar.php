<?php
//echo $tagList;
?><ul class=taglist ><?
foreach($rows as $value){
			$tagsize=$value["tagsize"];
			?><li class='tagsize<?=$tagsize;?>' title='<?=$value["num"];?>'><a href=/users/<?=$blogLogin;?>/tags/<?=$value["Name"];?>/ ><?=$value["Name"];?></a></li> <?
		}
		?></ul><?
echo $text;
?>
