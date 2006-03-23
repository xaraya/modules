function init() {
	var formObj = document.forms[0];
	var list = tinyMCE.getWindowArg('list')
	switch (tinyMCE.getWindowArg('listStyleType')) {
		case "decimal":
			formObj.decimalId.checked = true;
			break;
		case "lower-alpha":
			formObj.lowerAlphaId.checked = true;
			break;
		case "upper-alpha":
			formObj.upperAlphaId.checked = true;
			break;
		case "lower-roman":
			formObj.lowerRomanId.checked = true;
			break;
		case "upper-roman":
			formObj.upperRomanId.checked = true;
			break;
		case "disc":
			formObj.discId.checked = true;
			break;
		case "circle":
			formObj.circleId.checked = true;
			break;
		case "square":
			formObj.squareId.checked = true;
			break;
		case "none":
			formObj.noneId.checked = true;
			break;
		default:
			if (list == "ol") {
				formObj.decimalId.checked = true;
			}
			else {
				formObj.discId.checked = true;
			}
	}
	if (list == "ol") {
		document.getElementById("discRow").style.display = "none";
		document.getElementById("circleRow").style.display = "none";
		document.getElementById("squareRow").style.display = "none";
	}
	else {
		document.getElementById("decimalRow").style.display = "none";
		document.getElementById("laRow").style.display = "none";
		document.getElementById("uaRow").style.display = "none";
		document.getElementById("lrRow").style.display = "none";
		document.getElementById("urRow").style.display = "none";
	}
	formObj.insert.value = tinyMCE.getLang('lang_' + tinyMCE.getWindowArg('mceDo'));
}

function setListStyleType(listStyleType) {
	document.forms[0].listStyleTypeId.value = listStyleType;
}

function styleList() {
	var formObj = document.forms[0];
	var listStyleType = formObj.listStyleType.value;
	var selectedElement = tinyMCE.selectedInstance.getFocusElement();
	if (selectedElement != null) {
		while (selectedElement.nodeName != "LI")
			selectedElement = selectedElement.parentNode
		var listElement = tinyMCE.getParentElement(selectedElement, "ol,ul");
		if (listElement != null){
			listElement.style.listStyleType = listStyleType;
		}
	}
	tinyMCEPopup.close();
}

function cancelAction() {
	tinyMCEPopup.close();
}
