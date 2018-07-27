<?
$today=date("Y-m-d");
if(sizeof($commentsOfEntries))
{
?>
<TABLE border='0' cellpadding='0' cellspacing='0'>
<thead>
<TR class='tHeader'>
<TD>&nbsp;</TD>
<TD>Text</TD>
<TD>Login</TD>
<TD>Dátum</TD>
</TR>
</thead>
<tbody>
<?
$data=splitByDates($commentsOfEntries);
$count=0;
foreach($data as $key=>$value)
{
	$date=$key;
	if($key!=$today)
	{
?>
<TR class='tDate'>
<TD colspan=4><?=$key;?></TD>
</TR>
<?
	}
	foreach($value as $k=>$r)
	{
	if($count%2)
		{
			$class="Row1";
		}
		else
		{
				$class="Row0";
		}
			$class_access="all";
			if($r["access"]=="REGISTERED")
			  $class_access="registered";
			elseif($r["access"]=="FRIENDS" or $r["access"]=="LIST")
			  $class_access="friends";

$count++;
				?>
			<TR class='<?=$class;?>'>
			<td class=<?=$class_access;?> >&nbsp;</td>
			<TD class=listbody><?=stripslashes($r["body"]);?> &nbsp;</TD>
			<TD><?=$r["userID"];?></TD>
			<TD><a href=/users/<?=$r["diaryID"];?>/m_<?=$r["entryID"];?>/#a_<?=$r["ID"];?> class=anchor > <?=$r["datum"];?></a></TD>
			</tr>
			<?


	}
}
?>
</tbody>

</TABLE>
<? }
else echo "<span>Ez egy teljesen szűz terület</span>";
?>

