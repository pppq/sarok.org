var isIE;
var isGecko;
var isSafari;
var isKonqueror;
var gmap=false;
var ua = navigator.userAgent.toLowerCase();
	isIE = ((ua.indexOf("msie") != -1) && (ua.indexOf("opera") == -1) && (ua.indexOf("webtv") == -1));
	isGecko = (ua.indexOf("gecko") != -1);
	isSafari = (ua.indexOf("safari") != -1);
	isKonqueror = (ua.indexOf("konqueror") != -1);
var editable;
var currentRTE;
var isDirty=false;
var loadList=false;
var backup=false;
//alert("Loaded");
function unload()
{
if(!isDirty) return true;
if(document.getElementById("editableForm") && confirm("Itt mintha belekezdél volna valami szerkesztésbe. Mented?"))
{
if(editable.designMode=="on")
 document.getElementById("innereditable_textarea").value=document.getElementById("innereditable").contentWindow.document.body.innerHTML
isDirty=false;
document.getElementById("editableForm").submit();
}
return false;
}

function getEditableValue()
{
//var ed=TinyMCE_Control.getBody();
if(!nonEditable )
	{
	var bodyvalue= tinyMCE.getContent();
	//alert('huj');
	}
else 
	{
	var bodyvalue= document.getElementById("innereditable").value;
	//bodyvalue=bodyvalue.replaceAll("\n","<br />\n");
	//alert(bodyvalue);
	}
//alert(bodyvalue);
return bodyvalue;
}

function saveBackup()
{
//alert("saveBackup");
val=getEditableValue();
document.getElementById("backup").innerHTML=updateAData("saveBackup", val ,"backup");
setTimeout("saveBackup()", 30000);
}

function cedit()
{
//window.onunload="unload()";
if(loadList==true)
{
setTimeout("loadAll()", 10);
}
try{
if(checkNewMail==true) 
{setTimeout("checkMail()", 100);
}
}
catch(Ex)
{
}

//alert(gmap);
if(gmap==true)
{
makeGMap();
}

//if(!document.getElementById("innereditable")) return;

if(backup==true)
{
//alert("saveBackup");
setTimeout("saveBackup()", 30000);
}

resizeimages();
//alert("Loading");
//alert(additionalParams);
/*if(!isIE){
editable=document.getElementById("innereditable").contentDocument;
}
else
{
editable=document.getElementById("innereditable");
editable.contenteditable="true";
}*/

}

function resizeimages()
{
for(i=0;i<document.images.length; i++)
{
	image=document.images[i];
//	alert(image.width);
	if(image.width>350)
	{
		if(!isIE)
		{
		image.oldwidth=image.width;
		image.oldheight=image.height;
		}
		else
		{
		image.oldwidth=image.width;
		image.oldheight=image.height;
/*		alert(image.getPropertyValue("margin"));*/
		}
/*		alert(image.height);*/
		image.newheight=350*image.oldheight/image.oldwidth;
/*		alert(image.oldwidth);
		alert(image.oldheight);
		alert(image.newheight);*/
		image.width=350;
		image.height=image.newheight;
		image.onmouseover=function(){this.width=this.oldwidth;this.height=this.oldheight};
		image.onmouseout=function(){this.width=350;this.height=this.newheight};
		
	}
}
}

document.onkeydown = NavigateThrough;

function NavigateThrough (event)
{
	if (!document.getElementById) return;

	if (window.event) event = window.event;

	if (event.ctrlKey)
	{
		var link = null;
		switch (event.keyCode ? event.keyCode : event.which ? event.which : null)
		{
			case 0x25:
				link = document.getElementById ('NextLink');
				break;
			case 0x27:
				link = document.getElementById ('PrevLink');
				break;
			case 0x26:
				link = document.getElementById ('UpLink');
				break;
			case 0x28:
				link = document.getElementById ('DownLink');
				break;
		}

		if (link && link.href) document.location = link.href;
	}			
}

