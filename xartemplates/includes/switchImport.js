function xar_uploads_switchImport(iTypeId, iObjectId) {

    switch ( iTypeId ) {
        case 5: // turn on trusted import and off everything else
            document.getElementById(iObjectId + "_attach_trusted").style.display = "block";
            document.getElementById(iObjectId + "_attach_external").style.display = "none";
            document.getElementById(iObjectId + "_attach_upload").style.display = "none";
            document.getElementById(iObjectId + "_attach_stored").style.display = "none";

            document.getElementById(iObjectId + '_attach_external_id').value = '';
            break;
        case 2: // turn on external import and off everything else
            document.getElementById(iObjectId + "_attach_trusted").style.display = "none";
            document.getElementById(iObjectId + "_attach_external").style.display = "block";
            document.getElementById(iObjectId + "_attach_upload").style.display = "none";
            document.getElementById(iObjectId + "_attach_stored").style.display = "none";

            document.getElementById(iObjectId + '_attach_trusted_id').value = null;
            break;
        case 1: // turn on upload import and off everything else
            document.getElementById(iObjectId + "_attach_trusted").style.display = "none";
            document.getElementById(iObjectId + "_attach_external").style.display = "none";
            document.getElementById(iObjectId + "_attach_upload").style.display = "block";
            document.getElementById(iObjectId + "_attach_stored").style.display = "none";

            document.getElementById(iObjectId + '_attach_trusted_id').value = null;
            document.getElementById(iObjectId + '_attach_external_id').value = '';
            break;
        case 7: // turn on stored and off everything else
            document.getElementById(iObjectId + "_attach_trusted").style.display = "none";
            document.getElementById(iObjectId + "_attach_external").style.display = "none";
            document.getElementById(iObjectId + "_attach_upload").style.display = "none";
            document.getElementById(iObjectId + "_attach_stored").style.display = "block";

            document.getElementById(iObjectId + '_attach_stored_id').value = null;
            document.getElementById(iObjectId + '_attach_external_id').value = '';
            break;
        case 6: // clear stored value
            document.getElementById(iObjectId + "_attach_stored").style.display = "none";
            document.getElementById(iObjectId + "_attach_upload").style.display = "none";
            document.getElementById(iObjectId + "_attach_external").style.display = "none";
            document.getElementById(iObjectId + "_attach_trusted").style.display = "none";
            break;
        default: // reset
            document.getElementById(iObjectId + "_attach_stored").style.display = "none";
            document.getElementById(iObjectId + "_attach_upload").style.display = "none";
            document.getElementById(iObjectId + "_attach_external").style.display = "none";
            document.getElementById(iObjectId + "_attach_trusted").style.display = "none";
            
            initialMethod = document.getElementById(iObjectId + '_initial_method').value;
            switch (parseInt(initialMethod)) 
            {
                case 5: document.getElementById(iObjectId + "_attach_trusted").style.display = "block";break;
                case 2: document.getElementById(iObjectId + "_attach_external").style.display = "block";break;
                case 1: document.getElementById(iObjectId + "_attach_upload").style.display = "block";break;
                case 7: document.getElementById(iObjectId + "_attach_stored").style.display = "block";break;
            }
            break;
    }
    document.getElementById(iObjectId + '_active_method').value = iTypeId;

    return true;
}
