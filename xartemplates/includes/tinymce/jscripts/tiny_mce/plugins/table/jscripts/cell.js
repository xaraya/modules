function insertTable() {
	var args = new Array();

	args["width"] = document.forms[0].width.value;
	args["height"] = document.forms[0].height.value;
	args["align"] = document.forms[0].align.options[document.forms[0].align.selectedIndex].value;
	args["valign"] = document.forms[0].valign.options[document.forms[0].valign.selectedIndex].value;
	args["className"] = document.forms[0].styleSelect.options[document.forms[0].styleSelect.selectedIndex].value;
	args["bordercolor"] = document.forms[0].bordercolor.value;
	args["bgcolor"] = document.forms[0].bgcolor.value;
	args["cellType"] = document.forms[0].cellType.options[document.forms[0].cellType.selectedIndex].value;

	tinyMCEPopup.execCommand("mceTableCellProps", false, args);
	tinyMCEPopup.close();
}

function init() {
	if (tinyMCE.settings['table_color_fields']) {
		document.getElementById('colors').style.display = tinyMCE.isMSIE ? 'block' : 'table-row';
	}

	document.forms[0].bordercolor.value = tinyMCE.getWindowArg('bordercolor');
	document.forms[0].bgcolor.value = tinyMCE.getWindowArg('bgcolor');

	for (var i=0; i<document.forms[0].align.options.length; i++) {
		if (document.forms[0].align.options[i].value == tinyMCE.getWindowArg('align'))
			document.forms[0].align.options.selectedIndex = i;
	}

	for (var i=0; i<document.forms[0].valign.options.length; i++) {
		if (document.forms[0].valign.options[i].value == tinyMCE.getWindowArg('valign'))
			document.forms[0].valign.options.selectedIndex = i;
	}

	for (var i=0; i<document.forms[0].cellType.options.length; i++) {
		if (document.forms[0].cellType.options[i].value == tinyMCE.getWindowArg('cellType'))
        document.forms[0].cellType.options.selectedIndex = i;
	}

	var className = tinyMCE.getWindowArg('className');
	var styleSelectElm = document.forms[0].styleSelect;
	var stylesAr = tinyMCE.getParam('theme_advanced_styles', false);
	if (stylesAr) {
		stylesAr = stylesAr.split(';');

		for (var i=0; i<stylesAr.length; i++) {
			var key, value;

			key = stylesAr[i].split('=')[0];
			value = stylesAr[i].split('=')[1];

			styleSelectElm.options[styleSelectElm.length] = new Option(key, value);
			if (value == className)
				styleSelectElm.options.selectedIndex = styleSelectElm.options.length-1;
		}
	} else {
		var csses = tinyMCE.getCSSClasses(tinyMCE.getWindowArg('editor_id'));
		for (var i=0; i<csses.length; i++) {
			styleSelectElm.options[styleSelectElm.length] = new Option(csses[i], csses[i]);
			if (csses[i] == className)
				styleSelectElm.options.selectedIndex = styleSelectElm.options.length-1;
		}
	}

	var formObj = document.forms[0];
	formObj.width.value = tinyMCE.getWindowArg('width');
	formObj.height.value = tinyMCE.getWindowArg('height');
}
