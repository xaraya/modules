function xar_uploads_switchImport(sType, iTypeId, iObjectId) {
	// Fetch all the elements in advance, just for a little clarity.
	var elem_trusted = document.getElementById(iObjectId + "_attach_trusted");
	var elem_external = document.getElementById(iObjectId + "_attach_external");
	var elem_upload = document.getElementById(iObjectId + "_attach_upload");
	var elem_stored = document.getElementById(iObjectId + "_attach_stored");

	var elem_trusted_id = document.getElementById(iObjectId + "_attach_trusted_id");
	var elem_external_id = document.getElementById(iObjectId + "_attach_external_id");
	var elem_stored_id = document.getElementById(iObjectId + "_attach_stored_id");

	var elem_type = document.getElementById(iObjectId + "_attach_type");
	var elem_clear = document.getElementById('attach_clear_' + iObjectId);

	switch ( sType ) {
        case 1:
        case 2:
        case 3:
        case 4:
			// Ensure the 'clear' checkbox is not set.
			if (elem_clear) {elem_clear.checked = false;}
            if (elem_type) {elem_type.value = iTypeId;}
			break;
	}

	switch ( sType ) {
        case 1: // turn on trusted import and off everything else
            if (elem_trusted) {elem_trusted.style.display = "block";}
            if (elem_external) {elem_external.style.display = "none";}
            if (elem_upload) {elem_upload.style.display = "none";}
            if (elem_stored) {elem_stored.style.display = "none";}

            if (elem_external_id) {elem_external_id.value = '';}
            break;

		case 2: // turn on external import and off everything else
            if (elem_trusted) {elem_trusted.style.display = "none";}
            if (elem_external) {elem_external.style.display = "block";}
            if (elem_upload) {elem_upload.style.display = "none";}
            if (elem_stored) {elem_stored.style.display = "none";}

            if (elem_trusted_id) {elem_trusted_id.value = null;}
            break;

		case 3: // turn on upload import and off everything else
            if (elem_trusted) {elem_trusted.style.display = "none";}
            if (elem_external) {elem_external.style.display = "none";}
            if (elem_upload) {elem_upload.style.display = "block";}
            if (elem_stored) {elem_stored.style.display = "none";}

            if (elem_trusted_id) {elem_trusted_id.value = null;}
            if (elem_external_id) {elem_external_id.value = '';}
            break;

		case 4: // turn on stored and off everything else
            if (elem_trusted) {elem_trusted.style.display = "none";}
            if (elem_external) {elem_external.style.display = "none";}
            if (elem_upload) {elem_upload.style.display = "none";}
            if (elem_stored) {elem_stored.style.display = "block";}

            // 2008-11-10 JDJ: If we clear the stored ID value, then we lose the
            // previously-stored slected files. I don't know what the reasoning
            // was for blanking these files out.
            //if (elem_stored_id) {elem_stored_id.value = null;}
            if (elem_external_id) {elem_external_id.value = '';}
            break;

		case 6: // clear stored value, i.e. remove all files
            if (elem_trusted) {elem_trusted.style.display = "none";}
            if (elem_external) {elem_external.style.display = "none";}
            if (elem_upload) {elem_upload.style.display = "none";}
            if (elem_stored) {elem_stored.style.display = "none";}

            if (elem_type) {elem_type.value = (elem_clear.checked ? -2 : -1);}
            break;

		case 5:
        default: // reset, i.e. don't make any changes to the files
            if (elem_trusted) {elem_trusted.style.display = "none";}
            if (elem_external) {elem_external.style.display = "none";}
            if (elem_upload) {elem_upload.style.display = "none";}
            if (elem_stored) {elem_stored.style.display = "none";}
            
            if (elem_type) {elem_type.value = -1;}
            if (elem_clear) {elem_clear.checked = false;}
            break;
    }

    return true;
}
