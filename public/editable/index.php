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
<H1>D�ci Tomi</H1><IMG alt="felett�bb gyan�s" src="http://images.google.com/images?q=tbn:vJyjCk0NA0oJ:www4.informatik.uni-erlangen.de/%7Emsrex/images/how/suspicious.gif">Anya tal�lt otthon egy banksz�mlaszerz�d�st, ami a k�vetkez� n�vre volt ki�ll�tva: D�ci J�zsef Tam�s. A dologhoz tudni kell, hogy az ap�mat D�ci J�zsefnek h�vj�k. A m�sodik neve pedig Antall, de abban m�r nem vagyok biztos, hogy ez szerepel is a sz�let�si anyak�nyvi kivonat�ban. �n is csak onnan tudom, hogy a nagymam�m k�vetkezetesen T�nik�nak sz�l�totta. (B�r ez lehet, hogy m�r az �relmeszesed�s jele volt.)<BR><BR>Mindenestre gyan�s ez a Tam�s dolog. �gy t�nik, apa titkol valamit. A k�vetkez�eket tudom elk�pzelni: <BR>
<OL>
<LI>Ap�nak k�t �lete van. D�ci J�zsef strat�giai igazgat�k�nt dolgozik, k�t l�nya van, Gabi �s Edina, �s egy kuty�ja, akit Petinek h�vnak. D�ci Tam�s m�rleghinta karbantart�k�nt dolgozik, h�t fia van, Csabi, Bandi, Laci, Laci2, Laci3 �s Laci4, egy macija, Laci 7, �s egy elektromos fogkef�je, Laci 4632. (Igen, velem is megeshet, hogy h�rom magyar fi�n�vn�l nem jut t�bb az eszembe.) Kezdem �rteni, mi�rt l�tom olyan ritk�n ap�t h�tv�genk�nt.
<LI>Apa egy titkos�gyn�k. Fed�neve: Tam�s, a szak�cs.
<LI>A banksz�mlakivonat nem ap��, hanem eddig el�ttem is titkolt, gardr�bban t�rolt, mut�ns �csik�m�, a n�gyl�b� J�zsika Tomik��.
<LI>Apa megkapta Tam�s b�tya szerep�t Spielberg �j filmj�ben.
<LI>Apa nem is apa val�j�ban, hanem egy alien, aki csak beugrott helyettes�teni, am�g ap�t fogva tartj�k az idegenek, hogy kivegy�k az agy�t, �s a hely�be egy alm�t tegyenek.
<LI>Apa mindig is Tam�s szeretett volna lenni. �gy amikor csak teheti, azt hazudja idegeneknek, hogy Tam�snak h�vj�k.
<LI>Val�j�ban engem h�vnak D�ci J�zsef Tam�snak, csak ezt eddig nem mert�k el�rulni a sz�leim. Att�l tartottak, nem �r�ln�k neki, ha kider�lne, hogy be voltak r�gva az anyak�nyvez�sn�l. Vagy hogy egy fiatal feminim f�rfi vagyok. </LI></OL>Annyira drukkolok, hogy ne a hatos legyen a helyes megold�s. </textarea>
</div>
</body>
</html>
