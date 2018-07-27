<?
$today=date("Y-m-d");
if(is_array($mails) && sizeof($mails))
{
?>

<TABLE border='0' cellpadding='0' cellspacing='0'>
<thead>
<TR class='tHeader'>
<TD>Tárgy</TD>
<TD>Ki</TD>
<TD>Dátum</TD>
<TD></TD>
</TR>
</thead>
<tbody>
<?
$count=0;
$data=splitByDates($mails,"date");

foreach($data as $key=>$value)
{
	$date=$key;
	if($key!=$today)
	{
?>
<TR class='tDate'>
<TD colspan=4><?=human_date($key);?></TD>
</TR>
<?
	}
	foreach($value as $k=>$r)
	{

		if($count%2)
			$class="Row1";
		else
			$class="Row0";
		$count++;
			if($r["isRead"]=="N")
			  $class="newMail";
/*			elseif($r["access"]=="FRIENDS" or $r["access"]=="LIST")
			  $class="friends";
			else $class="Row1";*/
		?>
			<TR class='<?=$class;?>'>
			<TD class=listbody id='mail_<?=$r["ID"];?>' ><a href=/mail/<?=$r["ID"];?>/ class=anchor><?=stripslashes($r["title"]);?></TD>
			<TD><a href=/users/<?=$r["Login"];?>/ ><?=$r["Login"];?>&nbsp;</a></TD>
			<TD><?=$r["date"];?></TD>
			<td><a href='javascript:void(0)' onclick='this.style.display="none";updateAData("removePrivate","<?=$r["ID"];?>","mail_<?=$r["ID"];?>")' ><img src='/images/default/cross_small.gif' border=0 title='privát törlése' ></a></td>
			</tr>
			<?


	}
}

?>
</tbody>
</TABLE>
<? }
else {?>
	<span class=info>Ez egy teljesen szűz terület</span>
	<? } ?>