<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"

    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<? global $gen_hostname,$editableType; ?>
<base href="http://<?=$gen_hostname?>/" />
<link rel="stylesheet" type="text/css" href="css/<?=$skinName;?>/default.css" />
<link rel="stylesheet" type="text/css" href="css/<?=$skinName;?>/blog.css" />
<link rel="stylesheet" type="text/css" href="css/<?=$skinName;?>/entry.css" />
<link rel="stylesheet" type="text/css" href="css/mce.css" />
<?=$header;?>
<script src="/javascript/ajax.js"  TYPE="text/javascript" ></script>
<script src="/javascript/editable.js" TYPE="text/javascript" ></script>
<script src="/javascript/shortcut.js" TYPE="text/javascript" ></script>
<script src="/javascript/rate.js" TYPE="text/javascript" ></script>
<?if($editableType!="")
{ ?>
<script language="javascript" type="text/javascript" src="/editable/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript" src="/editable/tinyconfig_<?=$editableType;?>.js"></script>
<? } ?>

</head>
<body class=container onload='cedit()' onunload='return unload()'>


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
		<div class=header>
			<div class=logo>
				<a href="/" ><img src="images/saroklogo.png" border="0" ></a>
			</div>
	<div class=navigation>
			<?=$navigation;?>
			<?=$calendar;?>
			</div>

		<hr style="visibility: hidden; clear: both;" />
		</div>


		<div class=middle>
		<div class=main>
<?=$main?>

		</div>
<div class=sidebar>
<span id=newmail ><?=$newmail?></span>
<?=$sidebar;?>
<script type="text/javascript"><!--
google_ad_client = "pub-0029540167374287";
google_ad_width = 180;
google_ad_height = 150;
google_ad_format = "180x150_as";
google_ad_type = "text";
google_ad_channel ="";
google_color_border = "CCCCCC";
google_color_bg = "FFFFFF";
google_color_link = "000000";
google_color_url = "666666";
google_color_text = "333333";
//--></script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">

</script>

<?=$leftMenu?>

<div class=commonblogs>
<? require("../cache/public_blogs.php"); ?>
</div>


<?=$friendlist;?>

<center><br/><br/><a href="http://www.neteasy.hu"><img src=/images/hunnet_logo.gif border=0 /></a></center>
</div>
	<hr style="visibility: hidden; clear: both;" />
</div>

<div class=bottom>
<div><?=$navigation;?><br/>
&copy;2009 Sarok.org<br/></br/>
<a href='http://www.contextured.com/'>Search marketing</a>
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
