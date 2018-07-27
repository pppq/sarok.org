<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Content Editable</title>
	<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body onload='cedit()'>
<h1>Content Editable Test</h1>
<script src="editable.js"></script>

<div id='editable'>
<div id='panel' >
<span id=buttons>
<a href='javascript:void(0)' class='control_b' name='control_b' tabindex="-1" onclick='rteCommand("bold", null)'></a>
<a href='javascript:void(0)' class='control_i' name='control_i' tabindex="-1" onclick='rteCommand("italic", null)'></a>
<a href='javascript:void(0)' class='control_u' name='control_u' tabindex="-1" onclick='rteCommand("underline", null)'></a>
<a href='javascript:void(0)' class='control_strike' name='control_strike' tabindex="-1" onclick='rteCommand("StrikeThrough",null)'></a>

<a href='javascript:void(0)' class='control_separator' tabindex="-1"></a>

<a href='javascript:void(0)' class='control_subscript' name='control_subscript' tabindex="-1" onclick='rteCommand("Subscript",null)'></a>
<a href='javascript:void(0)' class='control_supscript' name='control_supscript' tabindex="-1" onclick='rteCommand("Superscript",null)'></a>

<a href='javascript:void(0)' class='control_separator' tabindex="-1"></a>

<a href='javascript:void(0)' class='control_ordered' name='control_ordered' tabindex="-1" onclick='rteCommand("InsertOrderedList",null)'></a>
<a href='javascript:void(0)' class='control_unordered' name='control_unordered' tabindex="-1" onclick='rteCommand("InsertUnOrderedList",null)'></a>

<a href='javascript:void(0)' class='control_separator' tabindex="-1"></a>

<a href='javascript:void(0)' class='control_left' name='control_left' tabindex="-1" onclick='rteCommand("JustifyLeft",null)'></a>
<a href='javascript:void(0)' class='control_center' name='control_center' tabindex="-1" onclick='rteCommand("JustifyCenter",null)'></a>
<a href='javascript:void(0)' class='control_right' name='control_right' tabindex="-1" onclick='rteCommand("JustifyRight",null)'></a>

<a href='javascript:void(0)' class='control_separator' tabindex="-1"></a>

<a href='javascript:void(0)' class='control_indent' name='control_indent' tabindex="-1" onclick='rteCommand("Indent",null)'></a>
<a href='javascript:void(0)' class='control_unindent' name='control_unindent' tabindex="-1" onclick='rteCommand("Outdent",null)'></a>
<!--
<a href='javascript:void(0)' class='control_separator' tabindex="-1"></a>

<a href='javascript:void(0)' class='control_link' name='control_link' tabindex="-1" onclick='rteCommand("Indent",null)'></a>
<a href='javascript:void(0)' class='control_image' name='control_image' tabindex="-1" onclick='rteCommand("Outdent",null)'></a>
-->
</span>
<a href='javascript:void(0)' id='mode_switch' onclick="switchStyles()">&lt;HTML&gt;</a>
</div>
<iframe id='innereditable' contenteditable="true"  marginwidth='0' marginheight='0' hspace=0 vspace=0 frameborder=0 align='left'></iframe>
<textarea id='innereditable_textarea'>
<H1>Dóci Tomi</H1><IMG alt="felettébb gyanús" src="http://images.google.com/images?q=tbn:vJyjCk0NA0oJ:www4.informatik.uni-erlangen.de/%7Emsrex/images/how/suspicious.gif">Anya talált otthon egy bankszámlaszerzõdést, ami a következõ névre volt kiállítva: Dóci József Tamás. A dologhoz tudni kell, hogy az apámat Dóci Józsefnek hívják. A második neve pedig Antall, de abban már nem vagyok biztos, hogy ez szerepel is a születési anyakönyvi kivonatában. Én is csak onnan tudom, hogy a nagymamám következetesen Tónikának szólította. (Bár ez lehet, hogy már az érelmeszesedés jele volt.)<BR><BR>Mindenestre gyanús ez a Tamás dolog. Úgy tûnik, apa titkol valamit. A következõeket tudom elképzelni: <BR>
<OL>
<LI>Apának két élete van. Dóci József stratégiai igazgatóként dolgozik, két lánya van, Gabi és Edina, és egy kutyája, akit Petinek hívnak. Dóci Tamás mérleghinta karbantartóként dolgozik, hét fia van, Csabi, Bandi, Laci, Laci2, Laci3 és Laci4, egy macija, Laci 7, és egy elektromos fogkeféje, Laci 4632. (Igen, velem is megeshet, hogy három magyar fiúnévnél nem jut több az eszembe.) Kezdem érteni, miért látom olyan ritkán apát hétvégenként.
<LI>Apa egy titkosügynök. Fedõneve: Tamás, a szakács.
<LI>A bankszámlakivonat nem apáé, hanem eddig elõttem is titkolt, gardróbban tárolt, mutáns öcsikémé, a négylábú Józsika Tomikáé.
<LI>Apa megkapta Tamás bátya szerepét Spielberg új filmjében.
<LI>Apa nem is apa valójában, hanem egy alien, aki csak beugrott helyettesíteni, amíg apát fogva tartják az idegenek, hogy kivegyék az agyát, és a helyébe egy almát tegyenek.
<LI>Apa mindig is Tamás szeretett volna lenni. Így amikor csak teheti, azt hazudja idegeneknek, hogy Tamásnak hívják.
<LI>Valójában engem hívnak Dóci József Tamásnak, csak ezt eddig nem merték elárulni a szüleim. Attól tartottak, nem örülnék neki, ha kiderülne, hogy be voltak rúgva az anyakönyvezésnél. Vagy hogy egy fiatal feminim férfi vagyok. </LI></OL>Annyira drukkolok, hogy ne a hatos legyen a helyes megoldás. </textarea>
</div>
</body>
</html>
