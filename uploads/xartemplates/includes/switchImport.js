function switchImport(sType, iTypeId) {

    oForm = document.forms["post"];

    switch ( sType ) {
		case 1: // turn on trusted import and off everything else
			if (document.getElementById) {
				document.getElementById("attach_trusted").style.display = "block";
				document.getElementById("attach_external").style.display = "none";
				document.getElementById("attach_upload").style.display = "none";
				document.getElementById("attach_stored").style.display = "none";
			} else {
				document.attach_trusted.style.display = "block";
				document.attach_external.style.display = "none";
				document.attach_upload.style.display = "none";
				document.attach_stored.style.display = "none";
			}

			oForm.attach_external.value = null;
			oForm.attach_type.value = iTypeId;
			break;
		case 2: // turn on external import and off everything else
			if (document.getElementById) {
				document.getElementById("attach_external").style.display = "block";
				document.getElementById("attach_trusted").style.display = "none";
				document.getElementById("attach_upload").style.display = "none";
				document.getElementById("attach_stored").style.display = "none";
			} else {
				document.attach_external.style.display = "block";
				document.attach_trusted.style.display = "none";
				document.attach_upload.style.display = "none";
				document.attach_stored.style.display = "none";
			}

			oForm.elements['attach_trusted[]'].value = null;
			oForm.attach_type.value = iTypeId
			break;
		case 3: // turn on upload import and off everything else
			if (document.getElementById) {
				document.getElementById("attach_upload").style.display = "block";
				document.getElementById("attach_external").style.display = "none";
				document.getElementById("attach_trusted").style.display = "none";
				document.getElementById("attach_stored").style.display = "none";
			} else {
				document.attach_upload.style.display = "block";
				document.attach_external.style.display = "none";
				document.attach_trusted.style.display = "none";
				document.attach_stored.style.display = "none";
			}

			oForm.elements['attach_trusted[]'].value = null;
			oForm.attach_external.value = null;
			oForm.attach_type.value = iTypeId
			break;
		case 4: // turn on upload import and off everything else
			if (document.getElementById) {
				document.getElementById("attach_stored").style.display = "block";
				document.getElementById("attach_upload").style.display = "none";
				document.getElementById("attach_external").style.display = "none";
				document.getElementById("attach_trusted").style.display = "none";
			} else {
				document.attach_stored.style.display = "block";
				document.attach_upload.style.display = "none";
				document.attach_external.style.display = "none";
				document.attach_trusted.style.display = "none";
			}

			oForm.elements['attach_stored[]'].value = null;
			oForm.attach_external.value = null;
			oForm.attach_type.value = iTypeId
			break;
		default: 
			if (document.getElementById) {
				document.getElementById("attach_stored").style.display = "none";
				document.getElementById("attach_upload").style.display = "none";
				document.getElementById("attach_external").style.display = "none";
				document.getElementById("attach_trusted").style.display = "none";
			} else {
				document.attach_stored.style.display = "none";
				document.attach_upload.style.display = "none";
				document.attach_external.style.display = "none";
				document.attach_trusted.style.display = "none";
			}
			oForm.attach_type.value = 0;
			break;
	}

	return true;
}
