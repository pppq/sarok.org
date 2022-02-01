tinyMCE.init({
mode : "textareas",
	editor_selector : "editable",
	theme : "advanced",
	width : "100%",
	height: "600",
	plugins : "table, searchreplace,contextmenu,fullscreen, flash",
	theme_advanced_toolbar_align : "left",
	theme_advanced_toolbar_location : "top",
	theme_advanced_buttons1 : "bold,italic,underline, strikethrough, separator, justifyleft, justifyright, justifycenter, justifyright, justifyfull, separator, fullscreen", 
	theme_advanced_buttons1_add : " separator, link, unlink, image, flash, separator, hr, spearator, formatselect, sub, sup",


	theme_advanced_buttons3 : "tablecontrols",
	table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
	table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
	table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
	table_cell_limit : 100,
	table_row_limit : 5,
	table_col_limit : 5,	
	language : "en",
	content_css : "/css/mce_style.css",
	relative_urls : false,
	apply_source_formatting : true,
	auto_focus : "mce_editor_0",
	dialog_type : "window",
	file_browser_callback :	"myCustomFileBrowser", 
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