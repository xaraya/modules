<?php

/*FUNC*/function uploads_user_uploadform()
{
    xarModAPILoad('uploads'); 
//    echo 'success:: ';
//    echo xarModAPIFunc('uploads', 'user', 'hashfilename', array('ulfile' => 'test.gif'));        

  //this is a sample/generic upload form for testing purposes.
    
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditArticles')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
      $data['maximum_file_size'] = xarModGetVar('uploads','maximum_upload_size');
    
    return $data;
}
?>