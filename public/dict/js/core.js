// Some global variables that detect which browser we are using
var ua = navigator.userAgent.toLowerCase();
var	isIE = ((ua.indexOf("msie") != -1) && (ua.indexOf("opera") == -1) && (ua.indexOf("webtv") == -1));
var	isGecko = (ua.indexOf("gecko") != -1);


js= {
		name: 'core',
		description: 'core routines',
		debug: false,
		debugID: "debug",
		debugCount: 0
	}

/**
	if element is a string, return an object with id element. If it is an object, returns itself
*/
function $(element) {
	//alert(typeof element);
	if(typeof element == 'object') return element;
	return document.getElementById(element)?document.getElementById(element):null;
 
}

/**
	Hides an object
*/

js.hide=function(obj)
		{
			obj=$(obj);
			obj.style.display='none';
		}
/**
	Shows an object
*/
js.show=function(obj)
		{
			obj=$(obj);
			if(!isIE)
			{
			switch(obj.tagName)
			{
				case 'TR':  obj.style.display='table-row';	break;
				case 'TBODY': obj.style.display='table-row-group';	break;
				case 'TABLE': obj.style.display='table';	break;
				case 'TD': obj.style.display='table-cell';	break;
				case 'TH': obj.style.display='table-row-cell';	break;
				default:	obj.style.display='block'; break;
			}
			}
			else
			{
			switch(obj.tagName)
			{
/*				case 'TR':  obj.style.display='table-row';	break;
				case 'TD': obj.style.display='table-cell';	break;
				case 'TH': obj.style.display='table-row-cell';	break;*/
				default:	obj.style.display='block'; break;
			}
			}
		}


/**
If object is visible, hides it, otherwise displays it.

*/
js.toggle=function(obj)
		  {
			obj=$(obj);
			if(obj.style.display=='none')
			{
				js.show(obj);
			}
			else
			{
				js.hide(obj);
			}
		  }

/**
js.log(params...)
Performs logging. Concatenates params into one string and appends it into the content of the textarea with id specifed in js.debugID

if this textarea does not exist, creates it on the 
*/
js.log=function()
		{
			if(!js.debug) {
				if($(js.debugID)) js.hide(js.debugID); 
			return;
			}
			var out="";
			for(var i=0;i<arguments.length;i++)
				out=out + arguments[i];
			if(!document.getElementById(js.debugID))
			 {
			 document.lastChild.innerHTML=document.lastChild.innerHTML+"<textarea name=debug id='"+js.debugID+"' ></textarea>";
			 // js.debugID=$(js.debugID);
			 }	
			//alert(out);
			
				$(js.debugID).value=js.getDate()+":"+js.debugCount+": "+out+"\n\n"+$(js.debugID).value;
				js.debugCount++;
				
		}

		
js.cleanLog=function()
		{
		if(!js.debug) return;
		$(js.debugID).value="";
		}

js.error=function(e)
{
		js.log("Error: ",e.name," ",e.message);
}

js.getUUID=function()
		{
			var id = new UUID();
			return id.toString();
		}
		
js.makeEditable=function(obj)
		{
			obj=$(obj);
			var content=obj.innerHTML;
			var id=js.getUUID()+10;
			var width=obj.offsetWidth+10;
			js.log(width,"px, content is: ",content);
			
			var text="<input type=text name='input_" + id + "' style='width:"+width+"px' id='"+id+"' onblur='js.blurEditable(this)'/>";
			obj.innerHTML=text;
			js.log($(id));
			$(id).value=content;
			$(id).initialValue=content;
			$(id).focus();
			obj.onclickOld=obj.onclick;
			obj.onclick=function() {};
		}

js.blurEditable=function(obj)
		{
		var obj=$(obj);
		js.log("blurring ", obj);
		var prevValue=obj.initialValue; 
		var curValue=obj.value;
		var	parent=obj.parentNode;
		parent.onclick=parent.onclickOld;
		parent.innerHTML=curValue;
		if(curValue!=prevValue && parent.onchange) 
		 {
			if(isIE) 
			{
				var uid=parent.uniqueID;
				var toEval=parent.onchange.replace("this","document.getElementById('"+uid+"')");
				js.log(toEval);
			  window.execScript(toEval);
			}
			else
			 {
			  parent.onchange();
			 }
		 }
		}

js.getParentByTagName=function(obj, tagName)
						{
						obj=$(obj);
						var tagName=tagName.toUpperCase()
						
							while(obj && obj.tagName!="BODY" && obj.tagName!="HTML")
							{
								if(!obj) return null;
								if(obj.tagName==tagName)
								 {
									js.log("getParentByTagName: found element "+tagName);
									return obj;
								}
								obj=obj.parentNode;
							}
							if(!obj) return null;
							if(tagName==obj.tagName) return obj;
							js.log("element not found");
							return(null);
														
							
						}

js.getParentByClass=function(obj, className)
						{
						obj=$(obj);
						var className=className.toUpperCase()
							while(obj && obj.tagName!="BODY" && obj.tagName!="HTML")
							{
								if(obj.className.toUpperCase()==className)
								 {
									js.log("getParentByClass: found element "+obj.tagName);
									return obj;
								}
								obj=obj.parentNode;
							}
							if(!obj) return null;
							js.log("element not found");
							return(null);
														
							
						}

js.getNextByTagName=function(node,obj, tagName)
						{
						obj=$(obj);
						tagName=tagName.toUpperCase();
						var elemList=node.getElementsByTagName("*");
						var len;
						var i;
						for(i=0,len=elemList.length;elemList[i]!=obj && i<len;i++)
						{
								//js.log("getNextByTagName: element ",i," is ",elemList[i].tagName);
						}
						i++;
						while(i<len && elemList[i].tagName!=tagName) i++;
						
						if(i<len)
							{	
								js.log("getNextByTagName: next element",obj.id," is under number ",i);
								return elemList[i];
							}
						else
							{
								js.log("getNextByTagName: object not found");
								return null;
							}							
						}


js.getNextByClassName=function(node,obj, className)
						{
						obj=$(obj);
						//className=className.toUpperCase();
						var elemList=node.getElementsByTagName("*");
						var len;
						var i;
						for(i=0,len=elemList.length;elemList[i]!=obj && i<len;i++)
						{
								//js.log("getNextByClassName: element ",i," is ",elemList[i].tagName," ",elemList[i].className);
						}
						i++;
						while(i<len &&  elemList[i].className!=className) i++;
						
						if(i<len)
							{	
								js.log("getNextByClassName: next element",obj.id," is under number ",i);
								return elemList[i];
							}
						else
							{
								js.log("getNextByClassName: object not found");
								return null;
							}								
						}


js.getElementsByClassName= function(node, className)
					{
					    var a = [];
					    var re = new RegExp('\\b' + className + '\\b');
					    node=$(node);
					    var els = node.getElementsByTagName("*");
					    var len;
					    for(var i=0,len=els.length; i<len; i++)
					        if(re.test(els[i].className))a.push(els[i]);
					    return a;
					}

js.evalScripts=function(obj)
					{
						js.log("evaling scripts");
						try{
						//js.log($(obj).innerHTML);
						var scripts=$(obj).getElementsByTagName("SCRIPT");
						 js.log("found ",scripts.length," scripts");
						 for(var i=0;i<scripts.length;i++)
						 {
 						    js.log("evaling ",scripts[i].innerHTML);
						 	eval(scripts[i].innerHTML);
						 }
						 }
						 catch(e)
						 {
						js.log("Error: ",e.name," -> ",e.message);
						 }
						
					}
					
js.toArray=function(list)
					{
					var len=list.length;
					var out=new Array();
					for(var i=0;i<len;i++)
					{
					out[i]=list[i];
					}
					return out;
					}

js.getURLParams=function(loc)
{
					if(!loc) {
					var loc=document.location.href;
				 	}
				 	var qs= loc;
				 	
					var av=qs.split('?');
					var url = new Array();
					url[0]=av[0];
					if(av.length==1) return url;
					qs= av[1];
					var nv = qs.split('&');
					
					for(var i = 0; i < nv.length; i++)
						{
						  var eq = nv[i].indexOf('=');
						  url[nv[i].substring(0,eq)] = unescape(nv[i].substring(eq + 1));
						}
					return(url);
}

js.setURLParams=function(loc,params)
{
	var url=js.getURLParams(loc);
	var baseURL=url[0];
	for(var p in params)
	{
		url[p]=params[p];
		
	}	
	var retURL=new Array();
	var i=0;
	for(var p in url)
	{
	   if(p!=0)
	   {
	   	retURL[i]=p+"="+url[p];
	   	i++;
	   }
	}
	return(baseURL+"?"+retURL.join("&"));
}

js.addURLParam=function(loc,name,value)
{
	var p=[];
	p[name]=value;
	
	return js.setURLParams(loc,p);
}

js.hasVisibleFields=function(formObj)
{
					for(el in formObj.elements)
					{
						if(!formObj.elements[el].type || formObj.elements[el].type!='hidden') return true;
					}
					return false;
}


js.replaceElement=function(obj,replacement)
{
				if(typeof replacement != "object")
				{
				var replacementText=replacement;
				replacement=document.createElement("span");
				replacement.innerHTML=replacementText;
				}
				js.insertAdjacentElement(obj,'beforebegin',replacement);
				js.hide(obj);
}

js.findLabelForInput=function(input)
{
				var sheet=js.getParentByTagName(input,"FORM");
				if(!sheet)
					var sheet=js.getParentByClass(input,"dataSheet");
					
				if(sheet)
					var labels=sheet.getElementsByTagName("LABEL");
				else
					var labels=document.getElementsByTagName("LABEL");
				var len=labels.length;
				for(var i=0;i<len;i++)
				{
					var label=labels[i];
					if(label.htmlFor==input.id)	return(label);
				}
				return input;
}

js.getDate=function()
	{
	return js.formatDate(new Date(),"HH:mm:ss");
	}	
	
function LZ(x) {return(x<0||x>9?"":"0")+x};
js.formatDate=function(date,format) {
	var MONTH_NAMES=new Array('January','February','March','April','May','June','July','August','September','October','November','December','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	var DAY_NAMES=new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sun','Mon','Tue','Wed','Thu','Fri','Sat');

	format=format+"";
	var result="";
	var i_format=0;
	var c="";
	var token="";
	var y=date.getYear()+"";
	var M=date.getMonth()+1;
	var d=date.getDate();
	var E=date.getDay();
	var H=date.getHours();
	var m=date.getMinutes();
	var s=date.getSeconds();
	var yyyy,yy,MMM,MM,dd,hh,h,mm,ss,ampm,HH,H,KK,K,kk,k;
	// Convert real date parts into formatted versions
	var value=new Object();
	if (y.length < 4) {y=""+(y-0+1900);}
	value["y"]=""+y;
	value["yyyy"]=y;
	value["yy"]=y.substring(2,4);
	value["M"]=M;
	value["MM"]=LZ(M);
	value["MMM"]=MONTH_NAMES[M-1];
	value["NNN"]=MONTH_NAMES[M+11];
	value["d"]=d;
	value["dd"]=LZ(d);
	value["E"]=DAY_NAMES[E+7];
	value["EE"]=DAY_NAMES[E];
	value["H"]=H;
	value["HH"]=LZ(H);
	if (H==0){value["h"]=12;}
	else if (H>12){value["h"]=H-12;}
	else {value["h"]=H;}
	value["hh"]=LZ(value["h"]);
	if (H>11){value["K"]=H-12;} else {value["K"]=H;}
	value["k"]=H+1;
	value["KK"]=LZ(value["K"]);
	value["kk"]=LZ(value["k"]);
	if (H > 11) { value["a"]="PM"; }
	else { value["a"]="AM"; }
	value["m"]=m;
	value["mm"]=LZ(m);
	value["s"]=s;
	value["ss"]=LZ(s);
	while (i_format < format.length) {
		c=format.charAt(i_format);
		token="";
		while ((format.charAt(i_format)==c) && (i_format < format.length)) {
			token += format.charAt(i_format++);
			}
		if (value[token] != null) { result=result + value[token]; }
		else { result=result + token; }
		}
	return result;
	}
	
js.insertAdjacentElement = function(obj,where, element) {
if(!isIE)
{
	switch (where.toLowerCase()) {
		case "beforebegin":
			obj.parentNode.insertBefore(element, obj);
			break;
		case "afterbegin":
			obj.insertBefore(element, obj.firstChild);
			break;
		case "beforeend":
			obj.appendChild(element);
			break;
		case "afterend":
			obj.parentNode.insertBefore(element, obj.nextSibling);
			break;
	}
}
else
{
obj.insertAdjacentElement(where,element);
}
	
}