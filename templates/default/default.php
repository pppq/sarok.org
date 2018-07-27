<?
global $refreshTime;
//$yesterday=date("Y-m-d");
?>
<script>
var loadList=true;
//alert(loadList);
function loadAll()
{
	//alert("huj");
	updateAData("getComments","","comments");
	//alert("huj");
	updateAData("getEntries","","entries");
	updateAData("getCommentsOfEntries","","commentsOfEntries");
	updateAData("getMyComments","","myComments");

	setTimeout("loadAll()",1000*<?=$refreshTime;?>);
}

</script>
<h2>Utolsó hozzászólások</h2>
<div class=list id=comments>
<span>Hello, Feri!</span>
</div>
<h2>Utolsó bejegyzesek</h2>
<div class=list id=entries>
<span>Milyen Feri?</span>
</div>

<h2>Utolsó hozzászólások a naplódhoz és a bejegyzéseidhez</h2>
<div class=list id=commentsOfEntries>
<span>...</span>
</div>

<h2>Utolsó hozzászólásaid</h2>
<div class=list id=myComments>
<span>...</span>
</div>