<?
$honapok["1"]="Január";
$honapok["2"]="Február";
$honapok["3"]="Március";
$honapok["4"]="Április";
$honapok["5"]="Májús";
$honapok["6"]="Június";
$honapok["7"]="Július";
$honapok["8"]="Augusztus";
$honapok["9"]="Szeptember";
$honapok["10"]="Október";
$honapok["11"]="November";
$honapok["12"]="December";
?>


				<?
				if(file_exists("../../www/userpics/<?=$blogName?>.jpg"))
				 {
				 	?>
				 <a href=/><img src="userpics/<?=$blogName?>.jpg" class=userpic border=0></a>
				<?}
				?>
<div class=info>
			<h2><a href="/users/<?=$blogName?>/" ><?=$blogName?></a></h2>
<? $bf=singletonloader::getInstance("blogfacade"); ?>
<script>
keyList[66]=function(){document.location="/users/<?=$blogName;?>/"; return true;};
keyTitle[66]="<?=$blogName;?> napló főlapja";
keyList[87]=function(){document.location="/users/<?=$blogName;?>/new/"; return true;};
keyTitle[87]="Új bejegyzés ebbe a naplóba";

</script>

<? if($entriesPerPage==$numRows) {?>
<a href="<?=$bf->makePath($blogName,array_merge($params,array("skip"=>$skip+$entriesPerPage)));?>" id='prevLink'>&lt; &lt;</a>
<script>
keyList[37]=function(){document.location=document.getElementById("prevLink").href; return true;};
keyTitle[37]="Régebbi bejegyzések";
</script>
<? }?>
<? if($skip>0) {?>
<a href="<?=$bf->makePath($blogName,array_merge($params,array("skip"=>max(0,$skip-$entriesPerPage))));?>"  id='nextLink'>&gt; &gt;</a>
<script>
keyList[39]=function(){document.location=document.getElementById("nextLink").href; return true;};
keyTitle[39]="Újabb bejegyzések";
</script>
<? }?>
</div>
	<form action="<?=$bf->makePath($blogName,array_merge($params,array("search"=>false, "skip"=> 0)));?>search/" method="post">
		<label for=search>Keresés a naplóban:</label>

		<input type=text name=keyword value="<?=$keyword;?>" id="search">	 <input type=image src="images/arrowgrey.png"  class=submit><br />
		<input type=checkbox id=commentsonly><label for=commentsonly>Csak a hozzászolásokban</label>

		</form>
			</div>
<div class='calendar'>
			<? if(sizeof($months)){?>
			<select name=month id=month onchange="document.location='<?=$bf->makePath($blogName,array_merge($params,array("year"=>"","month"=>"", "day"=>"","friends" =>$friends )));?>'+this.value" >
 <?
 foreach($months as $i=>$monthItem)
 {
 	//$monthItem=$months[$i];
 	$monthItem["m"]=max(1,$monthItem["m"]);
 	if($month==$monthItem["m"] and $year==$monthItem["y"]) $selected= "selected='true'";
 	else $selected= " ";
	echo "<option $selected value='".$monthItem["month"]."' >".$monthItem["y"]." ".$honapok[$monthItem["m"]]."</option>";
 }
?>
</select>
<? } ?>


