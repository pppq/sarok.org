<h2><?=sizeof($rows);?> bejegyzes</h2>
<? if(sizeof($rows)) { ?>
<form action=/settings/makeImport/ method=post >
<input type=submit value="Varazsolom">
<div class=list id=comments style='height: auto'>
<TABLE border='0' cellpadding='0' cellspacing='0'>
<thead>
<TR class='tHeader'>
<TD>Kell?</TD>
<TD>Text</TD>
<TD>Login</TD>
<TD>Dátum</TD>
<TD>Tegek</TD>
<TD>Mitcsinalni</TD>
</TR>
</thead>
<tbody>
<?
//print_r($rows);
$data=splitByDates($rows, "createDate");

foreach($data as $key=>$value)
{
	$date=$key;

?>
<TR class='tDate'>
<TD colspan=6><?=$key;?></TD>
</TR>
<?

	foreach($value as $k=>$r)
	{

			if($r["access"]=="REGISTERED")
			  $class="registered";
			elseif($r["access"]=="FRIENDS" or $r["access"]=="LIST")
			  $class="friends";
			else $class="Row1";
				?>
			<TR class='<?=$class;?>'>
			<td><input type=checkbox checked=true name="codes[]" value='<?=$r["index"];?>' id='check_<?=$r["index"];?>'></td>
			<TD class=listbody><label for='check_<?=$r["index"];?>'><?=strip_tags(stripslashes($r["body"]));?> &nbsp;</label></TD>
			<TD><?=$r["userID"];?></TD>
			<TD><?=$r["createDate"];?></TD>
			<TD><?=implode(",",$r["tagSet"]);?></TD>
			<TD><?=$r["action"];?></TD>
			</tr>
			<?


	}
}

?>
</tbody>
</TABLE>
</div>
<input type=submit value="Varazsolom">
</form>
<? } ?>