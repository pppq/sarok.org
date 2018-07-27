<? 
//print_r($_GET);
extract($_POST);
$total=120;
$total=isset($_GET["total"])?$_GET["total"]:$total;
if(isset($curPageNum))
{
$startPage=max(0,$curPageNum*$itemsPerPage); 
$endPage=min(($curPageNum+1)*$itemsPerPage,$total);
}
else
{
	$startPage=0;
	$endPage=$total;
}
if(isset($itemID) and is_numeric($itemID))
{
	$startPage=$itemID-1;
	$endPage=$itemID;
	$total=1;
}
//$endPage=$total;
?>
<table class=listing title="users">
<thead>
<tr>
<th>UserID</th>   
<td>Name</td>
<td>Email</td>
</tr>
</thead>
<?
for($i=$startPage+1;$i<$endPage+1;$i++)
{
	?>
<tbody id="<?=$i;?>">
<tr>
<th>user<?=$i;?>@accre.com</th>
<td>Name <?=$i;?></td>
<td>email<?=$i;?>@email.com</td>
</tr>
</tbody>

<? } ?>
</table>

<script>
totalItemsNum=<?=$total;?>; 
</script>