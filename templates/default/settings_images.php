<div class=banner>
<h1>Képek</h1>
</div>
<script>
//alert("huj");
function delImage(filename){
	//document.getElementById("li"+filename).style.visibility="none";
	//alert("huj");
	if(confirm("Bizti?"))
		updateAData("delImage",filename,"li_"+filename);
}
</script>
<form class='settings' action="/settings/images/set/" method="post" enctype="multipart/form-data" onsubmit="this.style.visibility='hidden'">

<fieldset>
<legend>Feltöltés</legend>
<table>
<tr>
<td class='key'>Feltöltés:</td>
<td class='value'>
  <input type="file" name="file"/>
</td>
</tr>
</table>
</fieldset>
<button type=submit name=submit value=aha>Feltöltöm</button>
</form>

<?if(isset($fileList) and is_array($fileList) and sizeof($fileList))
{

	foreach($fileList as $filename)
	{
		$files[substr($filename,0,10)][]=substr($filename,11,strlen($filename)-10);
	}
	krsort($files);
foreach($files as $day=>$days)
{
		echo "<h2>$day (".sizeof($days).")</h2>\n";
		?><ul class="imageList"><?
		sort($days);
		foreach($days as $sfilename)
		{
			$filename=$day."_".$sfilename;
			echo "<li id='li_$filename'>\n<img src='/images/default/cross_small.gif' class='img_delete' title='kép törlése'  onclick='delImage(\"$filename\")' />\n<a href=$path"."$filename ><img src=$path"."thumbs/$filename title=$filename ><br />$sfilename</a></li>\n";
		}
		?></ul><?
}


}
?>


