<? if($loggedin) { ?>
<! --Figyellek és teccel nekem--> All you base are belong to us, <span id="loggedIn"><a href=/users/<?=$name;?>/ ><?=$name;?></a></span>! [<a href="/logout/" >Lelépés</a>]
<? }
else {?>
<input type="text" name=login > <input type="password" name=pass>
<input type=hidden value=<?=$_SERVER["REQUEST_URI"]?> name=from >
 <input type=image src="images/arrowhite.png" border=0 class=submit>
<? } ?>