<?php

function uploads_user_upload() {
    
    xarVarFetch('importFrom', 'str:1:', $importFrom, NULL, XARVAR_NOT_REQUIRED); 
    
    $list = xarModAPIFunc('uploads','user','process_files', array('importFrom' => $importFrom));
    
    // FIXME: return the list of files uploaded/imported and show their status (added/errors)
    xarResponseRedirect(xarModURL('uploads', 'user', 'uploadform'));
}

?>
