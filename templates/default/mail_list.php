<a href=/privates/new/>Új privát irása</a>
<form method=post action="<?=$_SERVER['REQUEST_URI'];?>">
Keresés: <input type="text" name="keyword" value="<?=$keyword;?>" /><input type=submit value="Keres" />
</form>
<h2>Beérkezett űóüzenetek</h2>
<div class=list style='height: 30em'>
<?
$mails=$inmails;
require("mail_list.template.php");
?>
</div>


<h2>Elküldött üzenetek</h2>
<div class=list style='height: 30em'>
<?
$mails=$outmails;
require("mail_list.template.php");
?>
</div>