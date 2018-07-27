Elküldve nekik:
<ol>
<?
foreach($recipients as $recipient)
{
	?><ul><a href=/mail/from/<?=$recipient;?>/ ><?=$recipient;?></a></ul>
	<?
}
?>
</ol>