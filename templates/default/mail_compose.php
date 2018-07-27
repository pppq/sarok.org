<?
	extract($mail);

?>
<script src=/javascript/dropdown.js></script>
<form action=/mail/send/ method=post onsubmit='isDirty=false;if(editable.designMode=="on") document.getElementById("innereditable_textarea").value=document.getElementById("innereditable").contentWindow.document.body.innerHTML' >
<table class=mail >
<tr>
<td class='key'>Kitől:</td>
<td class='value'><a href=/mail/from/<?=$senderLogin;?>/ ><?=$senderLogin;?></a></td>
</tr>
<tr>
<td class='key'>Kinek:</td>
<td class='value'><input type=text name=recipient style='width:95%' value='<?=$recipientLogin;?>'  onfocus='dropdownInit(event,this,"getUserList")' autocomplete=off /></td>
</tr>
<tr>
<td colspan=2 class=body >
<input type='hidden' name='replyOn' value='<?=$replyOn;?>' />
<h2>Tárgy:<br /><input type=text name=title value='<?=$title;?>' style='width:95%' ></h2>
<?
global $gen_hostname;
$params="<base href='http://$gen_hostname/'>" .
		"<link rel='stylesheet' type='text/css' href='/css/$skinName/mail.css'>";
putEditable("body",stripslashes($body),$params,"mail");?>
</td>
</tr>
</table>
<input type=submit class="submit" value="Ahha" accesskey='s' >
</form>