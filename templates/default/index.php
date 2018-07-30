<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<? global $protocol, $gen_hostname, $editableType;?>
<base href="<?=$protocol?>://<?=$gen_hostname?>/" />
	<title>Sarok.org: az unatkozo haziasszonyok klubja.</title>
<link rel="stylesheet" type="text/css" href="css/<?=$skinName;?>/default.css" />
<link rel="stylesheet" type="text/css" href="css/<?=$skinName;?>/entry.css" />
<script src="/javascript/ajax.js" TYPE="text/javascript"></script>
<script src="/javascript/editable.js" TYPE="text/javascript"></script>
<script src="/javascript/shortcut.js" TYPE="text/javascript"></script>
<?
if($editableType!="")
{ ?>
<script language="javascript" type="text/javascript" src="/editable/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript" src="/editable/tinyconfig_<?=$editableType;?>.js"></script>
<? } ?>
</head>

<body class=container onload='cedit()' onunload='return unload()' >

	<div class=wrapper>
	<div class=center>
		<div class=top>
			<div class=menu>
				<?=$menu?>
			</div>
			<div class=login>
				<form  method=post action="auth">
					<?=$logout?>
				</form>
			</div>

		</div>
		<div class=middle>
		<div class=main>
<span id=newmail ><?=$newmail?></span>
<?=$main?>

		</div>
<div class=sidebar>
<?=$leftMenu?>

<div class=commonblogs>
<? require("../cache/public_blogs.php"); ?>
</div>
<?=$newmail;?>

<?=$friendlist;?>


</div>
	<hr style="visibility: hidden; clear: both;" />
</div>

<div class=bottom>
&copy;2006 Sarok.org
</div>
	</div>
</div>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-134859-1");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>
