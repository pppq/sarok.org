<?if(is_array($list) and sizeof($list))
{
    $oldDate=split(" ",$list[0]["activationDate"]);
    $oldDate=$oldDate[0];

    ?>
 <h3><a href="/users/<?=$login?>/friends/">Barátaid</a></h3>
<table class="friendlist" summary="A baratok listaja" border="0"
cellspacing="0" cellpadding="0">
<tr>
<td colspan="2" class="separator"><?=$oldDate;?></td>
</tr>

<?
for($i=0;$i<sizeof($list);$i++)
{
    $row=$list[$i];
    $date=split(" ",$row["activationDate"]);
    $time=$date[1];
    $date=$date[0];


    if(isset($online) and is_array($online) and
in_array($row["ID"],$online))
    {
        $on=true;

    }
    else
    {
        $on=false;
    }
?>
<?
if($date!=$oldDate)
{
    ?>
<tr>
<td colspan="2" class="separator"><?=$date;?></td>
</tr>

<?
}
$oldDate=$date;
?>
<tr <?=$on?"class='online'":""; ?>>
<td><a href='/users/<?=$row["login"];?>/'><?=$row["login"];?>
</a> </td>
<td class="date"><?=$time;?>
</td>
<? } ?>
</tr>
</table>

<? } ?>

