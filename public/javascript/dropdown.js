document.write("<ul id=dropdown></ul>");
var dropdown=document.getElementById("dropdown");
//dropdown.onkeyup=
var subject;
var dropdownIndex=-1;
var dropdownAction;
function dropdownInit(event,obj,action)
{
//dropdown.style.display='none';
//dropdown.style.display='block';
dropdownAction=action;
dropdown.innerHTML='';
subject=obj;
//alert(subject.offsetLeft);
//alert(subject.offsetTop);
subject.onkeyup=dropdownChange;
subject.onkeydown=dropdownNavigate;
subject.autocomplete="off";
//if (window.ActiveXObject) subject.addBehaviour("/javascript/selection.htc");
//subject.onfocusout=deactivateDropdown;
dropdown.onclick=dropdownClick;
//dropdown.ondblclick=dropdownClick;
dropdownActive=false;
//dropdownIndex=-1;
//alert(subject.onkeypress);
//alert(subject.offsetLeft);
//dropdown.style.left=subject.offsetLeft;
//dropdown.style.top=subject.offsetTop+subject.offsetHeight;
dropdown.style.left=getLeft(subject)+"px";
dropdown.style.top=getTop(subject)+subject.offsetHeight+"px";

//dropdown.offsetWidth=subject.offsetWidth;
if(subject.value.length>0)
		{
			dropdown.innerHTML="<li>"+updateAData2(dropdownAction,subject.value,getSelectionStart(subject),"dropdown")+"</li>";
			activateDropdown();
		}
		else
		{
			deactivateDropdown();
		}
}

function dropdownChange(event)
{
//alert(getSelectionStart(subject))
if(window.event)
	e=window.event;
else
e=event;
//alert(e.keyCode);
if((e.keyCode>40 || e.keyCode<35 || e.keyCode==37 || e.keyCode==39) && e.keyCode!=13)
{
		if(subject.value.length>0)
		{
//		alert("huj");
			dropdown.innerHTML="<li>"+updateAData2(dropdownAction,subject.value,getSelectionStart(subject),"dropdown")+"</li>";
			activateDropdown();
		}
return true;
}
else
{
if(e.keyCode==13) deactivateDropdown();
return false;
}
}

function dropdownNavigate(event)
{
if(window.event)
	e=window.event;
else
e=event;
//alert(e.keyCode);
switch(e.keyCode)
{
	case 40: dropdownIndex++; activateDropdown(); return 2; break;
	case 38: dropdownIndex--; activateDropdown(); return 2; break;
	case 27: deactivateDropdown(); return false; break;
	case 37: return 2; break;
	case 39: return 2; break;
	case 36: return 2; break;
	case 35: return 2; break;
	case 9:
	case 13:
			//alert(e.keyCode);
			if(dropdown.childNodes.length>0)
			{
			for(i=0;i<dropdown.childNodes.length;i++)
				{
				if(dropdown.childNodes[i].className=="selected")
				{
				//	alert(i);
					subject.value=dropdown.childNodes[i].title;
					deactivateDropdown();
					if(e.keyCode==13)
						return false;
					else return true;
				}
				}

			 }
			 deactivateDropdown();
			if(e.keyCode==13)
					return false;
				else return true;
			break;
}
return 1;
}

function dropdownClick(event)
{
if(window.event)
	e=window.event;
else
e=event;
//alert(e.srcElement.innerHTML);
if(e.srcElement)
{
//	text=e.srcElement.innerHTML;
	text=e.srcElement.title;
}
	else
{
//text=e.target.innerHTML;
text=e.target.title;
}
subject.value=text;
//alert("huj");
deactivateDropdown();
}
/*

*/
function activateDropdown()
{
dropdownActive=true;
	dropdown.style.display='block';
if(dropdown.childNodes.length>0)
{
window.status=dropdownIndex;
//alert(dropdownIndex);
if(dropdownIndex!=-1)
	dropdownIndex=(dropdownIndex+dropdown.childNodes.length)%dropdown.childNodes.length;
/*else
	dropdownIndex=-1;*/
//if(dropdownIndex>1) j=1; else j=0;
j=0;
		for(i=0;i<dropdown.childNodes.length;i++)
		{
		if(dropdown.childNodes[i].tagName=="LI")
		{
			if(j==dropdownIndex)
			{
			dropdown.childNodes[i].className="selected";
			}
			else
			{
			dropdown.childNodes[i].className="";
			}
			j++;
			}
		}
}
}

function deactivateDropdown()
{
dropdownActive=false;
	dropdown.style.display='none';
}

function getLeft(object)
{
obj=object;
left=0;
	while(obj.tagName!="BODY" && obj.tagName!="HTML")
	{
//		alert(obj.tagName+' '+left);
		left+=obj.offsetLeft;
		obj=obj.offsetParent;
	}
return left ;
}

function getTop(object)
{
obj=object;
var top=0;
while(obj.tagName!="BODY" && obj.tagName!="HTML")
{
//alert(obj.tagName);
top+=obj.offsetTop;
//alert(obj.tagName+' '+top);
obj=obj.offsetParent;
}
//alert(top);
return(top);
}

function getSelectionStart(object)
{
var start=0;
if(subject.selectionStart) start=subject.selectionStart;
else
{
//document.selection.createRange();
//alert(document.selection.type);
}
if (window.ActiveXObject) start-=-1;
//alert(start);
return start;
}