<?php

function uploads_user_upload() 
{
    
    if (!xarSecurityCheck('AddUploads')) return;
    
    xarVarFetch('importFrom', 'str:1:', $importFrom, NULL, XARVAR_NOT_REQUIRED); 
    
    $list = xarModAPIFunc('uploads','user','process_files', 
                           array('importFrom' => $importFrom));
    
    if (is_array($list) && count($list)) {
        return array('fileList' => $list);
    } else {
        xarResponseRedirect(xarModURL('uploads', 'user', 'uploadform'));
    }
}

?>