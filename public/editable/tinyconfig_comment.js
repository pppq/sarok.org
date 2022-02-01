tinyMCE.init({
mode : "textareas",
	editor_selector : "editable",
	theme : "advanced",
	width : "100%",
	height: "500",
	plugins : "searchreplace ,fullscreen, flash",
	theme_advanced_toolbar_align : "left",
	theme_advanced_toolbar_location : "top",
	theme_advanced_buttons1 : "bold,italic,underline, strikethrough,  separator, fullscreen", 
	theme_advanced_buttons1_add : " separator, link, unlink, image, flash, separator, hr, spearator, formatselect, sub, sup",


	language : "en",
	content_css : "/css/mce_style.css",
	auto_focus : "mce_editor_0",
	dialog_type : "window",
	relative_urls : false,
	apply_source_formatting : true,
/*	file_browser_callback :	"myCustomFileBrowser", */
	fullscreen_settings : {
		theme_advanced_path_location : "top"
	}
	
});


function myCustomFileBrowser(field_name, url, type, win) {
	// Do custom browser logic
/*	alert(field_name);
	alert(url);
	alert(type);
	alert(win);*/
	var oWindow = window.open("/imageBrowser/", null, "height=500,width=400,status=yes,toolbar=no,menubar=no,location=no,resizable=yes");
	oWindow.parwin=win;
	//alert(win.document.forms.length);
//	win.document.forms[0].elements[field_name].value = field_name+' '+url+' '+ type+ ' ' + win;
	
}