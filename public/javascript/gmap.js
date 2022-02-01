var initP=new GLatLng(47.49847389581876,19.040164947509766);
var GLevel=13;
var mapMarker;
var map;
var showMarker=false;
var changePos=true;
var pList=new Array();
var names=new Array();
var text=new Array();
var currentOverlay;
function makeGMap()
{
map = new GMap2(document.getElementById("map"));
map.addControl(new GLargeMapControl ());
//alert("added control");
map.addControl(new GMapTypeControl());
//map.addControl(new GScaleControl());

//alert("added control");

map.setCenter(initP, GLevel,G_HYBRID_MAP); 
//alert("centered and zoomed");
mapMarker = new GMarker(initP);
if(showMarker)
	map.addOverlay(mapMarker);

for(i=0;i<pList.length;i++)
{
	map.addOverlay(pList[i]);
}
GEvent.addListener(map, 'click', mapClick );

}

function mapClick(overlay,point)
{
//  alert("huj");
//  center = map.getCenterLatLng();
if(overlay)
{
currentOverlay=overlay;
if(names[overlay.id])
{
	if(overlay!=mapMarker)
	{
		 html = "<a href=/users/"+names[overlay.id]+"/ >"+names[overlay.id]+"</a><br/><a href=/users/"+names[overlay.id]+"/info/ >info</a>";
	}
	else
	{
		 html = "Itt&nbsp;vagy&nbsp;Te";
	}
}
else
{
html=text[overlay.id];
}

try{
overlay.openInfoWindowHtml(html);
}
catch(e)
{
}
}
else if(changePos){
  map.removeOverlay(mapMarker);
  mapMarker= new GMarker(point);
  map.addOverlay(mapMarker);
  document.getElementById("posx").value=point.x;
  document.getElementById("posy").value=point.y;
  	if(document.getElementById("needsMap"))
	{
	//alert("Updated");
		document.getElementById("needsMap").checked=true;
	}
}
  //alert(point.x + ", " + point.y);
  //map.centerAndZoom(point,map.getZoomLevel());
//  return true;
}

function updateMap()
{
	posX=document.getElementById("posx").value;
	posY=document.getElementById("posy").value;
	map.removeOverlay(mapMarker);
	point=new GPoint(posX,posY);
	mapMarker= new GMarker(point);
	map.centerAndZoom(point,map.getZoomLevel());
//	alert(point.x);
	map.addOverlay(mapMarker);


	

}