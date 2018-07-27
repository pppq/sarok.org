<?php
require("fck/fckeditor.php");

$ed=new FCKeditor("name");

$ed->BasePath = 'fck/';
$ed->Value="хуй!";
$ed->Create();
?>
<button onclick="checkContent()">Click</button>
<textarea id="out">
Itt.
</textarea>
<script>
function checkContent()
{
	//alert(document.getElementById("name").value);
	document.getElementById("out").value=document.getElementById("name").value;
	
}
</script>
