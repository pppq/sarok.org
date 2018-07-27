var deletedItems=new Array();
var rowCount=3;
var isDirty=false;

var MONTH_NAMES=new Array('January','February','March','April','May','June','July','August','September','October','November','December','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
var DAY_NAMES=new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sun','Mon','Tue','Wed','Thu','Fri','Sat');
function LZ(x) {return(x<0||x>9?"":"0")+x}
function formatDate(date,format) {
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

function showForm()
{
document.getElementById("form").style.display="table-row";
document.getElementById("control").style.display="none";
}

function showControl()
{
document.getElementById("form").style.display="none";
document.getElementById("control").style.display="table-row";
document.getElementById("newName").value="";
document.getElementById("newValue").value="";
}

function getDate()
{
return formatDate(new Date(),"HH:mm:ss");
}

function addRow(name, value)
{
if(name=="")
{
	putError("Please enter something","newNameErr");
	return;
}
if(value=="")
{
	putError("Please enter IP","newValueErr");
	return;
}
content=document.getElementById('tabBody').innerHTML;
rowCount++;
newRow="<tr class='newItem' id='r"+rowCount+"'><th id='rn"+rowCount+"' title='"+name+"'>"+name+" <span class=message><br/>Added on "+getDate()+"</span></th><td id='rv"+rowCount+"' title='"+value+"' ><span class='value' onclick='performChange("+rowCount+")'>"+value+"</span></td><td><a href='javascript:deleteRow("+rowCount+")'>delete</a></td></tr>";
document.getElementById('tabBody').innerHTML=content+newRow;

showControl();
setDirty();
}

function deleteRow(id)
{

name=document.getElementById("rn"+id).title;
value=document.getElementById("rv"+id).title;

if(confirm("Are you sure you want to delete "+name+"/"+value+"?"))
{
oldval=document.getElementById("r"+id).innerHTML;
deletedItems[id]=oldval;
document.getElementById("r"+id).innerHTML="<th colspan='3' class='deletedItem'>"+name+"/"+value+" marked for deletion on "+getDate()+". <a href='javascript:unDelete("+id+")'>Undelete</a></th>";
setDirty();
}
}

function unDelete(id)
{
document.getElementById("r"+id).innerHTML=deletedItems[id];
}
function putError(msg,loc){
//alert(msg);
document.getElementById(loc).innerHTML="<br />"+msg;

}

function performChange(id)
{
value=value=document.getElementById("rv"+id).title;
oldval=document.getElementById("rv"+id).innerHTML;
deletedItems[id]=oldval;
//alert(oldval);
this.document.getElementById("rv"+id).innerHTML="<input type=text value='"+value+"' id='rvi"+id+"'><a href='javascript:changeValue("+id+")'>change</a> (<a href='javascript:cancelChangeValue("+id+")'>cancel</a>)";

}

function cancelChangeValue(id)
{
this.document.getElementById("rv"+id).innerHTML=deletedItems[id];
}

function changeValue(id){
value=this.document.getElementById("rvi"+id).value;
this.document.getElementById("rv"+id).title=value;

this.document.getElementById("rv"+id).innerHTML="<span class='value' onclick='performChange("+id+")'>"+value+"</span> <span class='message'> changed on "+getDate()+"</span>";
this.document.getElementById("r"+id).className="newItem";
setDirty();
}

function setDirty()
{
isDirty=true;
document.getElementById("status").innerHTML="*Table is changed. Last change on "+getDate();
document.getElementById("submitButton").disabled=false;
}

function save()
{
if(isDirty)
{
if(confirm("YARRRRR! Data will be written do the database! Press OK to confirm"))
{
document.getElementById("status").innerHTML="Saving...";
reset();
setTimeout("save2()",1500);
}
}
else
{
document.getElementById("status").innerHTML="Data is not changed";
}
}

function save2()
{

document.getElementById("status").innerHTML="Saved "+rowCount+" row(s) to database on "+getDate(); 
document.getElementById("submitButton").disabled=true;


}

function reset()
{
rowC=0;
var names=new Array();
var values=new Array();
for(i=1;i<=20;i++)
{

if(document.getElementById("r"+i))
{
if(document.getElementById("rn"+i) && document.getElementById("rv"+i))
{

names[rowC]=document.getElementById("rn"+i).title;
values[rowC]=document.getElementById("rv"+i).title;
rowC++;
}
}
}
document.getElementById('tabBody').innerHTML="";
for(i=0;i<rowC;i++)
{
name=names[i];
value=values[i];
rowCount=i+1;
newRow="<tr id='r"+rowCount+"'><th id='rn"+rowCount+"' title='"+name+"'>"+name+" </th><td id='rv"+rowCount+"' title='"+value+"' ><span class='value' onclick='performChange("+rowCount+")'>"+value+"</span></td><td><a href='javascript:deleteRow("+rowCount+")'>delete</a></td></tr>";

document.getElementById('tabBody').innerHTML=document.getElementById('tabBody').innerHTML+newRow;
}
}