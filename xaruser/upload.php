<?php

function filemanager_user_upload() 
{
    
    if (!xarSecurityCheck('AddFileManager')) return;
    
    xarVarFetch('importFrom', 'str:1:', $importFrom, NULL, XARVAR_NOT_REQUIRED); 
    
    $list = xarModAPIFunc('filemanager','user','process_files', 
                           array('importFrom' => $importFrom));
    
    if (is_array($list) && count($list)) {
        return array('fileList' => $list);
    } else {
        xarResponseRedirect(xarModURL('filemanager', 'user', 'uploadform'));
    }
}

?>