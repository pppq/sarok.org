<h1>Páciensek listája</h1>
<?
extract($stat);
?>
<p>Összesen <b><?=$numUsers; ?></b> felhasználónk van, abbol <b><?=$numActiveUsers; ?></b> valaha irt is valamit. Az utóbbi egy honapban <b><?=$numUsersLastMonth; ?></b> felhasználó lépett be. 
Összesen <b><?=$numEntries; ?></b> bejegyzést, <b><?=$numComments; ?></b> hozzászolást irtunk. </p>
<div class="list" style="height:auto">
<table>
<thead>
<th>Login</th>
<td>Aktivitás</td>
<td>Regisztrálás</td>

</thead>

<?php
$data=splitByDates($users,"activationDate");
foreach($data as $day=>$uList)
{
	?>
	<tbody>
	<tr class="tDate"><td colspan="3"><?=human_date($day);?> (<?=sizeof($uList);?>)</td></tr>
	<?
	foreach($uList as $u)
	{
		?>
		<tr>
		<th><a href="/users/<?=$u["login"];?>"><?=$u["login"];?></a></th>
		<td><?=$u["activationDate"];?></td>
		<td><?=human_time($u["createDate"]);?></td>
	<!--	<td><?=human_time($u["activationDate"]);?></td>
		<td><?=human_time($u["createDate"]);?></td>
-->		</tr>
	<?
	}
	?>
	
	</tbody>
	<?
}


//print_r($users);
?>
</table>
</div>