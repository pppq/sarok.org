<?
global $dayofweek;
$year=$yearmonth[0];
$month=$yearmonth[1];
$maxNum=getMaxDaysInMonth($year,$month);
$startDay=getWeekDate($year,$month,1);
?>
<h2><?=$honapneve;?></h2>
<h2>Napi adatok</h2>
<?
$visits=$obj->visits;
$comments=$obj->comments;
$entries=$obj->entries;
$selfComments=$obj->selfComments;

?>
<table>
<thead>
<tr>
<td>Nap</td>
<td>Latogató</td>
<td>Bejegyzés</td>
<td>Hozzászolás</td>
<td>Sajat hozzászolás</td>
</tr>
</thead>
<tbody>
<?
for($day=1;$day<=$maxNum;$day++)
{
	$dw=($startDay+$day-1)%7;
	if(!isset($visits[$day])) $visits[$day]=0;
	if(!isset($comments[$day])) $comments[$day]=0;
	if(!isset($entries[$day])) $entries[$day]=0;
	if(!isset($selfComments[$day])) $selfComments[$day]=0;
	$class="";
	if($dw>=5) $class="class='weekend' ";
	
	echo "<tr $class >";
	echo "<th>$day, ".$dayofweek[$dw]."</th>";
	echo "<td>{$visits[$day]}</td>";
	echo "<td>{$entries[$day]}</td>";
	echo "<td>{$comments[$day]}</td>";	
	echo "<td>{$selfComments[$day]}</td>";
	echo "</tr>";
}
?>
</tbody>
<tfoot>
<tr>
<th >Összesen</th>
<td><?=array_sum($visits)?></td>
<td><?=array_sum($entries)?></td>
<td><?=array_sum($comments)?></td>
<td><?=array_sum($selfComments)?></td>
</tr>
</tfoot>
</table>

<?
$entries=$obj->entryList;
if(is_array($entries) and sizeof($entries))
{
	arsort($entries);
?>
<h2>Legnépszerübb bejegyzéseid</h2>
<?
//sort($users);


?>
<table>
<thead>
<tr>
<td>Bejegyzés</td>
<td>Hányszor</td>
</tr>
</thead>
<tbody>
<?
$uLogin=$context->user->login;
foreach($entries as $u=>$count)
{
	echo "<tr>";
	echo "<th><a href='/users/$uLogin/m_$u/'>$u</a></th>";
	echo "<td>$count</td>";
	echo "</tr>";
}
?>
</tbody>
<tfoot>
<tr>
<td colspan="2">Összesen tehát <?=sizeof($entries)?> bejegyzés</td>
</tr>
</tfoot>

</table>
<? } ?>


<?
$users=$obj->users;
if(is_array($users) and sizeof($users))
{
	arsort($users);
?>
<h2>Leggyakoribb olvasóid</h2>
<?
//sort($users);


?>
<table>
<thead>
<tr>
<td>Felhasználónév</td>
<td>Hányszor</td>
</tr>
</thead>
<tbody>
<?
foreach($users as $u=>$count)
{
	echo "<tr>";
	echo "<th><a href='/users/$u/'>$u</a></th>";
	echo "<td>$count</td>";
	echo "</tr>";
}
?>
</tbody>
<tfoot>
<tr>
<td colspan="2">Összesen tehát <?=sizeof($users)?> felhasználó</td>
</tr>
</tfoot>

</table>
<? } ?>

<?
$ipList=$obj->ipList;

if(is_array($ipList) and sizeof($ipList))
{
	arsort($ipList);
?>
<h2>Leggyakoribb ip cimek</h2>
<?



?>
<table>
<thead>
<tr>
<td>IP</td>
<td>Hányszor</td>
</tr>
</thead>
<tbody>
<?
$c=0;
foreach($ipList as $ip=>$count)
{
	echo "<tr>";
	echo "<th>$ip</th>";
	echo "<td>$count</td>";
	echo "</tr>";
	if($c++>20) break;
}
?>
</tbody>
<tfoot>
<tr>
<td colspan="2">Összesen egyébként <?=sizeof($ipList)?> ip cim</td>
</tr>
</tfoot>
</table>
<? } ?>


<?
$referrers=$obj->referrers;
if(is_array($referrers) and sizeof($referrers))
{
	arsort($referrers);
?>
<h2>Leggyakoribb hivatkozo oldalak</h2>
<?

//sort($users);


?>
<table>
<thead>
<tr>
<td>URL</td>
<td>Hányszor</td>
</tr>
</thead>
<tbody>
<?
foreach($referrers as $u=>$count)
{
	echo "<tr>";
	echo "<th><a href='$u' title='$u'>".substr($u,0,40)."</a></th>";
	
	echo "<td>$count</td>";
	echo "</tr>";
}
?>
</tbody>

<tfoot>
<tr>
<td colspan="2">Összesen <?=sizeof($referrers)?> hivatkozás</td>
</tr>
</tfoot>
</table>
<?
}
?>
