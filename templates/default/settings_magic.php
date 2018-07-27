<h2><?=sizeof($rows);?> találat</h2>
<? if(sizeof($rows)) { ?>
<? if($action!="nothing") { ?>
<form action=/settings/makeMagic/ method=post >
<input type=submit value="Varazsolom">
<input type=hidden name=act value=<?=$action;?> >
<?if(isset($tags)) {?>
<input type=hidden name=tags value=<?=$tags;?> >
<? } ?>

<?if(isset($access)) {?>
<input type=hidden name=access value=<?=$access;?> >
<? } ?>

<?if(isset($comments)) {?>
<input type=hidden name=comments value=<?=$comments;?> >
<? } ?>

<? } ?>
<div class=list id=comments style='height: auto'>
<TABLE border='0' cellpadding='0' cellspacing='0'>
<thead>
<TR class='tHeader'>
<TD>Kell?</TD>
<TD>Text</TD>
<TD>Login</TD>
<TD>Dátum</TD>
</TR>
</thead>
<tbody>
<?
$data=splitByDates($rows, "createDate");

foreach($data as $key=>$value)
{
	$date=$key;

?>
<TR class='tDate'>
<TD colspan=4><a href=/users/<?=$diaryID;?>/<?=strtr($key,"-","/");?> target=_blank ><?=$key;?></a></TD>
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
			<td><input type=checkbox checked=true name="codes[]" value='<?=$r["ID"];?>' id='check_<?=$r["ID"];?>'></td>
			<TD class=listbody><label for='check_<?=$r["ID"];?>'><?=strip_tags(stripslashes($r["title"]));?> &nbsp;</label></TD>
			<TD><?=$logins[$r["userID"]];?></TD>
			<TD><a href=/users/<?=$r["diaryID"];?>/m_<?=$r["ID"];?>/ target=_blank > <?=$r["createDate"];?></a></TD>
			</tr>
			<?


	}
}

?>
</tbody>
</TABLE>
</div>
<? if($action!="nothing") { ?>
<input type=submit value="Varazsolom">
</form>
<? } ?>
<? } ?>