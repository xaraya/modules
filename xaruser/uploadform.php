<?php

function filemanager_user_uploadform() 
{
    
    if (!xarSecurityCheck('AddFileManager')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['file_maxsize'] = xarModGetVar('filemanager','file.maxsize');
    
    return $data;
}
?>