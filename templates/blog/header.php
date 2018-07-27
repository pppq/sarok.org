<? $bf=singletonloader::getInstance("blogfacade"); ?>
<link rel="alternate" type="text/xml" title="<?=$login;?> blogja teljes RSS 2.0" href="/users/<?=$login;?>/rss/" />
<? if("/users/$login/rss/"!=$rss)
{ ?><link rel="alternate" type="text/xml" title="Ez az oldal RSS 2.0" href="<?=$rss;?>" /> <? } ?>
<title><?=$title;?></title>
<link id="UpLink" href="/users/<?=$blogName?>" />
<? if($entriesPerPage==$numRows) {?>
<link rel="next" href="<?=$bf->makePath($blogName,array_merge($params,array("skip"=>$skip+$entriesPerPage)));?>" id="NextLink"/>
<? }?>
<? if($skip>0) {?>
<link rel="prev"  href="<?=$bf->makePath($blogName,array_merge($params,array("skip"=>max(0,$skip-$entriesPerPage))));?>"  id="PrevLink"/>

<? }?>

