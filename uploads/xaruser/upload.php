<?php

function uploads_user_upload() {
    
    xarVarFetch('importFrom', 'str:1:', $importFrom, NULL, XARVAR_NOT_REQUIRED); 
    
    $list = xarModAPIFunc('uploads','user','process_files', array('importFrom' => $importFrom));
    
    if (count($list['errors']) > 0) {
        return $list;
    } else {
        xarResponseRedirect(xarModURL('uploads', 'admin', 'view'));
    }
}

?>
