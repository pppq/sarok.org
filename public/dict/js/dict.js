var plugins=new Array();

plugins[0]="sztaki";
plugins[1]="yandex";
function getDict(w)
{
	var resultHTML="";
	for(var i=0;i<plugins.length;i++)
	{
		resultHTML+="<div id='"+plugins[i]+"' class=resultSheet ><blink>"+"Loading results from "+plugins[i]+"</blink></div>";
	}
		$('result').innerHTML=resultHTML;
	
	for(var i=0;i<plugins.length;i++)
	{
		var pluginURL="plugins/"+plugins[i]+".php?action=get&w="+w;
		ajax.loadURL(pluginURL,processResult,plugins[i]);
	}

}

function processResult(text,plugin)
{
	var src="<h3>"+plugin+":</h3>";
	if(text.length<5) src+="<span class='nomatch'>no matches</span>";
	else
	src+=text;
	$(plugin).innerHTML=src;
}