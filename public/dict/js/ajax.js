var ajax={
		name: 'ajax',
		description: 'ajax support module',
		charset:	'UTF-8',
		content_type:'application/x-www-form-urlencoded'
	}
/**
Retrieves a platform-independent AJAX transport.
*/
ajax.getTransport=function()
{
		var result = false;
		var actions = [
	      function() {return new XMLHttpRequest()},
	      function() {return new ActiveXObject('Msxml2.XMLHTTP')},
	      function() {return new ActiveXObject('Microsoft.XMLHTTP')}
	    ];
	    for(var i = 0; i < actions.length; i++) {
	    	try{
	    		result = actions[i]();
	    		js.log("Got transport via ",actions[i]);
	    		break;
	    	} catch (e) {}	
	    }
	    return result;
}


/**
ajax.loadURL(url,target,[params...])
In case if target is a function:
Retrieves the content of the url via GET and passes it to the target(content,[params])

otherwise:
Retrieves the content of the url via GET and inserts it into the target (target.innerHTML=content)

*/
ajax.loadURL=function(url,target)
{
	 	 js.log("loadURL(",url," ",target,")");
		 var http=ajax.getTransport();
		 var args=arguments;
		 try{
		 http.open("GET", url, true);
		 http.setRequestHeader("Method", "GET " + url + " HTTP/1.1");
	     http.setRequestHeader("Content-Type", ajax.content_type);
	 	 http.setRequestHeader("Charset", ajax.charset);
		 
		 
		 http.onreadystatechange= function()
		 {
			if (http.readyState == 4) 
			 {
//			   if(http.status==0 || http.status==200)
			   {

			   	js.log("status is ",http.status, " ",http.statusText);
			   
			   
			   	if(typeof target!="function")
			   	{
				   	if(!target) return;
			   		$(target).innerHTML=http.responseText;
			   		/*if(!notExec)
			   		 {

			   		 	js.evalScripts(target);
			   		 }*/
			   	}
			   	else
			   	{
				if(args.length<=2)
				{
			   		target(http.responseText);
			   	}
			   	else
			   	{
			   		var out=new Array();
			   		for(var i=2;i<args.length;i++) out[i-2]="args["+i+"]";
			   		eval("target(http.responseText,"+out.join(",")+")");
			   	}
			   	}
			   }
			   
			 }
		 };
		 
		 http.send(null);
		 }
		 catch(e)
		 {
		 js.log("AJAX ERROR: ",e);
		 return;
		 }
		  
}


/**
ajax.sendURL(url,params,target,[params...])

converts the params hash into a URI string and sends it via POST to the url, then:

In case if target is a function:
Retrieves the content of the url via POST and passes it to the target(content,[params])

otherwise:
Retrieves the content of the url via POST and inserts it into the target (target.innerHTML=content)

*/
ajax.sendURL=function(url,params,target)
{
	 	 js.log("sendURL(",url," ",target,")");
		 var http=ajax.getTransport();
		 var args=arguments;
		 try{
		 http.open("POST", url, true);
		 http.setRequestHeader("Method", "POST " + url + " HTTP/1.1");
	     http.setRequestHeader("Content-Type", ajax.content_type);
	 	 http.setRequestHeader("Charset", ajax.charset);
		 var toSend=new Array();
		 var i=0;
		 for(key in params)
		 {
			 if(typeof params[key]=='string')
			 {
				 toSend[i]=encodeURIComponent(key)+"="+encodeURIComponent(params[key]);
				 i++;
			 }
			 else
			 {
			 var pList=params[key];
			 js.log(key," is an array"); 
			 for(var j=0;j<pList.length;j++)
				{
				 toSend[i]=encodeURIComponent(key)+"="+encodeURIComponent(pList[j]);
				 i++;		 
				} 
			 }

		 }
		 sendStr=toSend.join("&")
		 http.send(sendStr);
		 js.log("sent ",sendStr);
		 }
		 catch(e)
		 {
		 js.log("AJAX ERROR: ",e);
		 return;
		 }
		 
		 http.onreadystatechange= function()
		 {
			if (http.readyState == 4) 
			 {
			   if(http.status>=0 || http.status==200)
			   {

			   	js.log("status is ",http.status, " ",http.statusText);
			   
			   
			   	if(typeof target!="function")
			   	{
			   	if(!target) return;
			   		$(target).innerHTML=http.responseText;
			   		/*if(!notExec)
			   		 {

			   		 	js.evalScripts(target);
			   		 }*/
			   	}
			   	else
			   	{
				if(args.length<=3)
				{
			   		target(http.responseText);
			   	}
			   	else
			   	{
			   		var out=new Array();
			   		for(var i=3;i<args.length;i++) out[i-3]="args["+i+"]";
			   		eval("target(http.responseText,"+out.join(",")+")");
			   	}
			   	}
			   }
			   else
			   {
			   	js.log("status is ",http.status, " ",http.statusText);
			   	e=new Error();
			   	e.message=http.status+" "+http.statusText;
			   	throw(e);
			   }
			 }
		 };
		  
}


/**
ajax.submitObj(url,obj,target,[params...])

Submits all enabled elements within obj to URL via POST, then:

In case if target is a function:
Retrieves the content of the url via POST and passes it to the target(content,[params])

otherwise:
Retrieves the content of the url via POST and inserts it into the target (target.innerHTML=content)

*/
ajax.submitObj=function(url,obj,target)
{
		obj=$(obj);
		var elemList=js.toArray(obj.getElementsByTagName("input"));
		elemList=elemList.concat(js.toArray(obj.getElementsByTagName("textarea")));
		elemList=elemList.concat(js.toArray(obj.getElementsByTagName("select"))); 
		var len=elemList.length;
		js.log(len, " elements found");
		var params=new Array();
		for(var i=0;i<len;i++)
			{
				elem=elemList[i];
				js.log(elem.name," type is ", elem.type, " value is ", elem.value);
				if(elem.disabled)
				{
				js.log(elem.name," is disabled, skipping it");
					continue;
				}
				if((elem.type=='checkbox' || elem.type=='radio') && !elem.checked) 
				{
					js.log(elem.name," is not checked, skipping it");
					continue;
				}
				if(elem.tagName=='SELECT' && elem.type=='select-multiple')
				{
				//TODO Actually there is a low-prio bug: 
				// this is the way how a mozilla handles multiple selects, 
				// this would not work in IE. 
				// but we don't have multiple selects in portal, so this is not critical.
				
				js.log("multiple select found");
					var options=elem.options;
					var selOptions=new Array();
					for(j=0;j<options.length;j++)
					   {
					   	if(options[j].selected)
					   	{
					   	  js.log(options[j].text," is selected with value ",options[j].value);
					   	  selOptions[selOptions.length]=options[j].value;
					   	  }
					   }
					params[elem.name]=selOptions;  
				}
				else
				{
					params[elem.name]=elem.value;
				}
			}
		if(arguments.length<=3)
				{
			   		ajax.sendURL(url, params, target);
			   	}
			   	else
			   	{
			   		var out=new Array();
			   		for(var i=3;i<arguments.length;i++) out[i-3]="arguments["+i+"]";
			   		var toEval="ajax.sendURL(url, params, target,"+out.join(",")+")";
			   		js.log("evaluating ",toEval);
			   		eval(toEval);
			   	}
}


/**
ajax.submitForm(obj,target,[params...])

Submits the form to the action of the form via POST, then

In case if target is a function:
Retrieves the content of the url via POST and passes it to the target(content,[params])

otherwise:
Retrieves the content of the url via POST and inserts it into the target (target.innerHTML=content)

*/
ajax.submitForm=function(obj,target)
{
				obj=$(obj);
				var formObj=js.getParentByTagName(obj,"FORM");
				var url=formObj.getAttribute("action");
				if(formObj.onunload)
					{
					if(formObj.onunload()==false)
						{
						return;
						}
					}
				else
				{
				if(formObj.onsubmit)
					{
					if(formObj.onsubmit()==false)
						{
						return;
						}
					}
				}
				
				js.log("Submitting to action ",url);
				if(arguments.length<=2)
				{
			   		ajax.submitObj(url, formObj, target);
			   	}
			   	else
			   	{
			   		var out=new Array();
			   		for(var i=2;i<arguments.length;i++) out[i-2]="arguments["+i+"]";
			   		var toEval="ajax.submitObj(url, formObj, target,"+out.join(",")+")";
			   		js.log("evaluating ",toEval);
			   		eval(toEval);
			   	}
}

	