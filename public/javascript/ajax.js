function updateAData(action,param,id)
{
if(window.XMLHttpRequest) {
var	http = new XMLHttpRequest();
}
else if (window.ActiveXObject) {
 var http = new ActiveXObject("Microsoft.XMLHTTP");
		}

//alert("huj");
  var url = "/quickActions.php";

	http.open("POST", url, true);
	http.setRequestHeader("Method", "POST " + url + " HTTP/1.1");
	http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	http.setRequestHeader("Charset", "utf-8");

//  http.send(null);
//  alert(param.replace("&","%26"));
//  http.send("Text="+param.replace("&","%26"));
//alert("huj");
  toSend="action="+encodeURIComponent(action)+"&text="+encodeURIComponent(param);
//  alert(toSend);
  http.send(toSend);
  http.onreadystatechange = function(){
  if(http.readyState == 4){
//	alert(http.responseText);
try{
	  document.getElementById(id).innerHTML=http.responseText;
	 }
	 catch(e){
	 document.getElementById(id).innerHTML="Valamely oknál fogva a böngészöd nem tudja megjeleniteni az előnézet. Valószinüleg azért, mert a bőngésződ jó szar. De nem kell félni, nem a Te hibád és a bejegyzéseddel sincs semmi gond, az nem veszett el. Kattintcsd vissza a TEXT-re"
	 }
//	alert(http.responseText);
	  }
	 else
	 {
	 //document.getElementById(id).innerHTML=document.getElementById(id).innerHTML+ " " + http.readyState;
	 }
  }

   return("<span class='wait'>Várj</span>");
}

function updateAData2(action,param1,param2,id)
{
if(window.XMLHttpRequest) {
var	http = new XMLHttpRequest();
}
else if (window.ActiveXObject) {
 var http = new ActiveXObject("Microsoft.XMLHTTP");
		}

//alert("huj");
  var url = "/quickActions.php";

	http.open("POST", url, true);
	http.setRequestHeader("Method", "POST " + url + " HTTP/1.1");
	http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	http.setRequestHeader("Charset", "utf-8");

//  http.send(null);
//  alert(param.replace("&","%26"));
//  http.send("Text="+param.replace("&","%26"));
//alert("huj");
  toSend="action="+encodeURIComponent(action)+"&text="+encodeURIComponent(param1)+"&text2="+encodeURIComponent(param2);
//  alert(toSend);
  http.send(toSend);
  http.onreadystatechange = function(){
  if(http.readyState == 4){
//	alert(http.responseText);
try{
	  document.getElementById(id).innerHTML=http.responseText;
	 }
	 catch(e){
	 document.getElementById(id).innerHTML="Valamely oknál fogva a böngészöd nem tudja megjeleniteni az előnézet. Valószinüleg azért, mert a bőngésződ jó szar. De nem kell félni, nem a Te hibád és a bejegyzéseddel sincs semmi gond, az nem veszett el. Kattintcsd vissza a TEXT-re"
	 }
//	alert(http.responseText);
	  }
	 else
	 {
	 document.getElementById(id).innerHTML=document.getElementById(id).innerHTML+ " " + http.readyState;
	 }
  }

   return("<span class='wait'>Várj</span>");
}