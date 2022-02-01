/*
altKey
altLeft IE5.5
behaviorCookie W5.5
behaviorPart W5.5
bookmarks W4
boundElements
button
cancelBubble
clientX
clientY
contentOverflow
ctrlKey
ctrlLeft IE5.5
dataFldW4
dataTransferW5
fromElement
keyCode
nextPage W5.5
offsetX
offsetY
propertyNameW5
qualifier W4
reasonW4
recordsetW4
repeatW5
returnValue
saveType W5.5
screenX
screenY
shiftKey
shiftLeft IE5.5
srcElement
srcFilterW4
srcUrnW5
toElement
type
wheelDataW6
xy


alinkColor
anchors[]
applets[]
attributes[]
bgColor
body
characterSet (1)
childNodes[]
compatMode W6,N7
cookie
defaultView (1)
doctype (1)
documentElement
domain
embeds[]
fgColor
firstChild
forms[]
height (1)
images[]
implementation (1)
lastChild
lastModified
linkColor
links[]
location
namespaceURI
nextSibling
nodeName
nodeType
ownerDocument (1)
parentNode
plugins[]
previousSibling
referrer
styleSheets[]
title
URL
vlinkColor
width(1)
onblur
onclick
ondblclick
onfocus
onkeydown
onkeypress
onkeyup
onmousedown
onmousemove
onmouseout
onmouseover
onmouseup
onresize

*/
/*document.write("<div id=shortcut></div>");
document.onkeydown=showShortcut;
document.onkeyup=hideShortcut;*/
var keyList=new Array();
var keyTitle=new Array();
keyList[77]=function(){document.location="/"; return true;};
keyTitle[77]="Főlap";
keyList[81]=function(){document.location="/logout/"; return true;};
keyTitle[81]="Kilépés";
keyList[87]=function(){document.location="/users/"+document.getElementById("loggedIn").innerHTML+"/new/"; return true;};
keyTitle[87]="Új bejegyzés";
keyList[66]=function(){document.location="/users/"+document.getElementById("loggedIn").innerHTML+"/"; return true;};
keyTitle[66]="Saját napló";
keyList[83]=function(){document.location="/settings/"; return true;};
keyTitle[83]="Beállitások";
keyList[72]=function(){
					if(document.getElementById("shortcut").style.display=='block')
					{
					document.getElementById("shortcut").style.display='none';
					document.getElementById("shortcut").innerHTML="";
					}
					else
					{
					mesg="";
						document.getElementById("shortcut").style.display='block';
						for(i=10;i<255;i++)
						{
						if(keyTitle[i])
						{
							mesg=mesg+"<dt>"+String.fromCharCode(i)+"</dt><dd>"+keyTitle[i]+"</dd>";
						}

						}
						document.getElementById("shortcut").innerHTML="<dl>"+mesg+"</dl>";
						document.getElementById("shortcut").focus();
					}


//						"<dl><dt></dt><dd></dd><dl>";

					return false;
					};
keyTitle[72]="Help";
keyList[71]=function(){
					if(document.getElementById("shortcut").style.display=='block')
					{
					document.getElementById("shortcut").style.display='none';
					document.getElementById("shortcut").innerHTML="";
					}
					else
					{
					document.getElementById("shortcut").style.display='block';
					mesg="";
					mesg+="<form action='javascript:document.location=\"/users/\"+document.getElementById(\"whereToGo\").value+\"/\"'>Kihez szeretnel menni: <input type=text id=whereToGo 	autocomplete=off ><input type=submit value=go></form>";

						document.getElementById("shortcut").innerHTML=mesg;
					keyList[27]=function(){document.getElementById("shortcut").style.display='none';}
					keyTitle[27]="Vissza";
						document.getElementById("whereToGo").focus();

					}


//						"<dl><dt></dt><dd></dd><dl>";

					return false;
					};
keyTitle[71]="Ugras masik naploba";

keyList[67]=function(){if(document.getElementById("innereditable"))
				{
				document.getElementById("innereditable").contentWindow.document.body.focus();
								return false;

				}
				else return false;};
keyTitle[67]="irás";

keyList[40]=nextElement;
keyTitle[40]="kov";

keyList[38]=prevElement;
keyTitle[38]="prev";

//alert("huj");
function showShortcut(event)
{
	if(!document.getElementById("loggedIn")) return true;
	var shortcut=document.getElementById("shortcut");
	if(event)
		e=event;
	else
		e=window.event;
	if(e.srcElement)
		tagName=e.srcElement.tagName;
	else
		tagName=e.target.tagName;
	if(tagName=="INPUT" || tagName=="TEXTAREA") return;
	if(e.altKey || ((e.keyCode==38 || e.keyCode==40) && !e.ctrlKey ) ) return;


	window.status=e.keyCode;
	if(keyList[e.keyCode])
	{
	//alert("huj");
	posY=50;
	if(e.screenY)
	 posY=e.screenY;
	if(e.pageY)
	 posY=e.pageY;

	shortcut.style.top=posY+"px";
	shortcut.style.left="100px";
	msg=false;
	if(keyList[e.keyCode]()) 	msg=keyTitle[e.keyCode];
	if(msg!=false)
	{

	shortcut.innerHTML="<H1>"+msg+"</H1>";
	shortcut.style.display="block";
	}
	return false;
	}

}

function hideShortcut(){
	var shortcut=document.getElementById("shortcut");
	//shortcut.style.display="none";
}

var actpos=0;
var eList=false;
function nextElement()
{
if(!eList) createEList()
if(!eList.length) return false;
actpos=(actpos+1)%eList.length;
eList[actpos].focus();
//eList[actpos].style.border="1px solid black";
//alert(eList[actpos].id);
return false;
}

function prevElement()
{
if(!eList) createEList()
if(!eList.length) return false;
actpos=(actpos+eList.length-1)%eList.length;
//alert(eList[actpos].id);
eList[actpos].focus();
return false;
}

function createEList()
{
eList=new Array();

all=document.getElementsByTagName("a");
for(el in all)
{
	e=all[el];
//	alert(el);
if(e.className=='anchor' || e.className=='comment')
   {
   eList.push(e);
//	  alert(e.id);
  }
}
//alert(eList.length);
}
