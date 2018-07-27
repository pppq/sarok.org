<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<? global $gen_hostname;

global $img_hostname;?>
<base href="http://<?=$gen_hostname?>/" />
	<title>Sarok.org</title>
<link rel="stylesheet" type="text/css" href="css/default/default.css" />
<link rel="stylesheet" type="text/css" href="css/default/entry.css" />
<script src="/javascript/ajax.js" TYPE="text/javascript"></script>
<script src="/javascript/editable.js" TYPE="text/javascript"></script>
<script src="/javascript/shortcut.js" TYPE="text/javascript"></script>
</head>
<body class=container style="padding-left: 20px">
<div class=center>
<div class=main>

<script>
function delImage(filename){
	if(confirm("Bizti?"))
		updateAData("delImage",filename,"li_"+filename);
}

function SelectFile( fileUrl )
{

         
         window.parwin.document.getElementById('src').value=fileUrl;

         
        window.close() ;
}
/*
function SelectFile( fileUrl )
{
        window.opener.SetUrl( fileUrl ) ;
        window.close() ;
}*/

</script>

<form class='settings' action="/settings/images/set/" method="post" enctype="multipart/form-data" onsubmit="this.style.visibility='hidden'">
<fieldset>
<legend>Feltöltés</legend>
<table>
<tr>
<td class='key'>Feltöltés:</td>
<td class='value'>
  <input type="file" name="file"/>
  <input type=hidden name="location" value="/imageBrowser/" />
</td>
</tr>
</table>
</fieldset>
<button type=submit name=submit value=aha>Feltöltöm</button>
</form>

<?
global $gen_hostname;
if(isset($fileList) and is_array($fileList) and sizeof($fileList))
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
			echo "<li id='li_$filename'>\n<img src='/images/default/cross_small.gif' class='img_delete' title='kép törlése'  onclick='delImage(\"$filename\")' />\n<a href=\"javascript:SelectFile('http://$img_hostname"."$path"."$filename');\" ><img src=$path"."thumbs/$filename title=$filename ><br />$sfilename</a></li>\n";
		}
		?></ul><?
}


}
?>

</div>
</div>
</body>
</html>


