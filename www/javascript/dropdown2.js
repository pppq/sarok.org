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
			dropdown.innerHTML="<li>"+updateAData(dropdownAction,subject.value,"dropdown")+"</li>";
			activateDropdown();
		}
		else
		{
			deactivateDropdown();
		}
}

function dropdownChange(event)
{
if(window.event)
	e=window.event;
else
e=event;
//alert(e.keyCode);
if((e.keyCode>40 || e.keyCode<35) && e.keyCode!=13)
{
		if(subject.value.length>0)
		{
//		alert("huj");
			dropdown.innerHTML="<li>"+updateAData(dropdownAction,subject.value,"dropdown")+"</li>";
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
	case 37: return 2; break;
	case 39: return 2; break;
	case 36: return 2; break;
	case 35: return 2; break;
	case 13:
			//alert(e.keyCode);
			if(dropdown.childNodes.length>0)
			{
			for(i=0;i<dropdown.childNodes.length;i++)
				{
				if(dropdown.childNodes[i].className=="selected")
				{
				//	alert(i);
					subject.value=dropdown.childNodes[i].innerHTML;
					return false;
				}
				}

			 }
			 deactivateDropdown();
			return false;
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
	text=e.srcElement.innerHTML;
}
	else
{
text=e.target.innerHTML;
}
subject.value=text;
//alert("huj");
deactivateDropdown();
}

function activateDropdown()
{
dropdownActive=true;
	dropdown.style.display='block';
if(dropdown.childNodes.length>0)
{
window.status=dropdownIndex;
dropdownIndex=(dropdownIndex+dropdown.childNodes.length)%dropdown.childNodes.length;
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