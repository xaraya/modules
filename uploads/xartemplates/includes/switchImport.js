function switchImport(sType, iTypeId) {

    oForm = document.forms["post"];

    switch ( sType ) {
		case 1: // turn on trusted import and off everything else
			if (document.getElementById) {
				document.getElementById("file_import_trusted").style.visibility = "visible";
				document.getElementById("file_import_external").style.visibility = "hidden";
				document.getElementById("file_import_upload").style.visibility = "hidden";
			} else {
				document.file_import_trusted.style.visibility = "visible";
				document.file_import_external.style.visibility = hidden;
				document.file_import_upload.style.visibility = "hidden";
			}

			oForm.file_import_external.value = null;
			oForm.dd_file_import_type.value = iTypeId;
			break;
		case 2: // turn on external import and off everything else
			if (document.getElementById) {
				document.getElementById("file_import_external").style.visibility = "visible";
				document.getElementById("file_import_trusted").style.visibility = "hidden";
				document.getElementById("file_import_upload").style.visibility = "hidden";
			} else {
				document.file_import_external.style.visibility = "visible";
				document.file_import_trusted.style.visibility = "hidden";
				document.file_import_upload.style.visibility = "hidden";
			}

			oForm.elements['file_import_trusted[]'].value = null;
			oForm.dd_file_import_type.value = iTypeId
			break;
		case 3: // turn on upload import and off everything else
			if (document.getElementById) {
				document.getElementById("file_import_upload").style.visibility = "visible";
				document.getElementById("file_import_external").style.visibility = "hidden";
				document.getElementById("file_import_trusted").style.visibility = "hidden";
			} else {
				document.file_import_upload.style.visibility = "visible";
				document.file_import_external.style.visibility = "hidden";
				document.file_import_trusted.style.visibility = "hidden";
			}

			oForm.elements['file_import_trusted[]'].value = null;
			oForm.file_import_external.value = null;
			oForm.dd_file_import_type.value = iTypeId
			break;
		default: 
			break;
	}

	return true;
}
