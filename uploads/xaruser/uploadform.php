<?php

function uploads_user_uploadform() {
    
    if (!xarSecurityCheck('EditArticles')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['file_maxsize'] = xarModGetVar('uploads','file.maxsize');
    
    return $data;
}
?>
