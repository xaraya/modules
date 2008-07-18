function xar_uploads_switchImport(iTypeId, iObjectId) {

    switch ( iTypeId ) {
        case 5: // turn on trusted import and off everything else
            document.getElementById(iObjectId + "_attach_trusted").style.display = "block";
            document.getElementById(iObjectId + "_attach_external").style.display = "none";
            document.getElementById(iObjectId + "_attach_upload").style.display = "none";
            document.getElementById(iObjectId + "_attach_stored").style.display = "none";

            document.getElementById(iObjectId + '_attach_external_id').value = '';
//            document.getElementById('attach_clear_' + iObjectId).checked = false;
            break;
        case 2: // turn on external import and off everything else
            document.getElementById(iObjectId + "_attach_trusted").style.display = "none";
            document.getElementById(iObjectId + "_attach_external").style.display = "block";
            document.getElementById(iObjectId + "_attach_upload").style.display = "none";
            document.getElementById(iObjectId + "_attach_stored").style.display = "none";

            document.getElementById(iObjectId + '_attach_trusted_id').value = null;
//            document.getElementById('attach_clear_' + iObjectId).checked = false;
            break;
        case 1: // turn on upload import and off everything else
            document.getElementById(iObjectId + "_attach_trusted").style.display = "none";
            document.getElementById(iObjectId + "_attach_external").style.display = "none";
            document.getElementById(iObjectId + "_attach_upload").style.display = "block";
            document.getElementById(iObjectId + "_attach_stored").style.display = "none";

            document.getElementById(iObjectId + '_attach_trusted_id').value = null;
            document.getElementById(iObjectId + '_attach_external_id').value = '';
//            document.getElementById('attach_clear_' + iObjectId).checked = false;
            break;
        case 7: // turn on stored and off everything else
            document.getElementById(iObjectId + "_attach_trusted").style.display = "none";
            document.getElementById(iObjectId + "_attach_external").style.display = "none";
            document.getElementById(iObjectId + "_attach_upload").style.display = "none";
            document.getElementById(iObjectId + "_attach_stored").style.display = "block";

            document.getElementById(iObjectId + '_attach_stored_id').value = null;
            document.getElementById(iObjectId + '_attach_external_id').value = '';
//            document.getElementById('attach_clear_' + iObjectId).checked = false;
            break;
        case 6: // clear stored value
            document.getElementById(iObjectId + "_attach_stored").style.display = "none";
            document.getElementById(iObjectId + "_attach_upload").style.display = "none";
            document.getElementById(iObjectId + "_attach_external").style.display = "none";
            document.getElementById(iObjectId + "_attach_trusted").style.display = "none";
            break;
        case 5:
        default: // reset
            document.getElementById(iObjectId + "_attach_stored").style.display = "none";
            document.getElementById(iObjectId + "_attach_upload").style.display = "none";
            document.getElementById(iObjectId + "_attach_external").style.display = "none";
            document.getElementById(iObjectId + "_attach_trusted").style.display = "none";
            
            document.getElementById(iObjectId + '_active_method').value = -1;
//            document.getElementById('attach_clear_' + iObjectId).checked = false;
            break;
    }
    document.getElementById(iObjectId + '_active_method').value = iTypeId;

    return true;
}
