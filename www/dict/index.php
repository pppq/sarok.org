
<html>
<head>
<meta http-equiv="Content-Language" content="en" />
<meta name="GENERATOR" content="PHPEclipse 1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Quick Dictionary</title>
<link rel="stylesheet" type="text/css" href="css/dict.css">
<script src="js/ajax.js" TYPE="text/javascript"></script>
<script src="js/core.js" TYPE="text/javascript"></script>
<script src="js/dict.js" TYPE="text/javascript"></script>
</head>
<body>
<div id="container">
<form onsubmit="getDict($('w').value);return false;">
Enter English word: <input type=text id=w >
<input type=submit value="GO">
</form>
<div id=result></div>
<div class=header>&copy; Demeter Sztanko</div>
</div>

</body>
</html>
  
<?php
/*
 * Created on 16 Jan 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
