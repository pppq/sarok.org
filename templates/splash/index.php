<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Sarok 3.0 -as verzio</title>
	<link rel="stylesheet" type="text/css" href="css/splashmagritte.css">
</head>

<body>
<table id=container cellpadding="0" cellspacing="0">
<tr>
<td class=header colspan=3>
<div>
 <form method=post action="auth">
 <h1>Belépés:</h1>
<table>
<tr>
<td>Login: </td><td><input type="text" name=login ></td>
</tr>
<tr>
 <td>Password: </td><td><input type="password" name=pass></td>
</tr>
 </table>
<button type=submit>Belepek</button>

<br /><br /><a href=/registration/>Regisztrálok</a>
<br /><a href=/about/>Olvasok az oldalrol</a>
<br /><a href=/users/>Megnézem a legutolsó bejegyzéseket</a>
<br /><?=$main;?>
</form>

</div>
</td></tr>
</table>


</body>
</html>


