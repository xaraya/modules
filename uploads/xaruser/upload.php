<?php

/*FUNC*/function uploads_user_upload()
{
  //this passes the data on to the userapi.  use this function as a template for
    //how to handle uploads in your own module.
    
    
    // Security check
    if (!xarSecurityCheck('ReadArticles')) return;
    // Confirm authorisation code.  
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) configuration',
                    'Example');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
        
        //this is the name of the field in the form for the upload file
        $inputfield = 'uploadfile';
        
        //this is the ID number of the newly added item within the module.
        //normally, a module would insert the item into the db and use
        //$dbconn->PO_Insert_ID() to retrieve the new value, or perhaps
        //use $dbconn->GenID() to find what the next value will be, upload the file,
        //and use information retrieved from the file to insert into the module item
        //table along with the item's other information
        $modid = 1;
        
        //this is the type of upload being requested.  you can choose to
        // a) keep the item in a file on the file system
        // b) upload the file to the db
        // c) return the text from the file.
        //types are: 'file', 'db', 'text'
        $uploadtype = 'file';
        
        /*this is the file extensions you will allow for uploading in this module.
        if you don't pass anything, the file extensions listed in uploads will be used.
        these must be semicolon(;) delimited with no end semicolon unless you want to 
        allow for uploads with no extension.
        */
        //$extensions = 'gif;jpg;png';
        
      // The API function is called.  if the 
        $info = xarModAPIFunc('uploads',
                              'user',
                              'upload',
                                array('uploadfile'=>$inputfield,
                                          'mod'=>'uploads',
                                                'modid'=>$modid,
                                                'utype'=>$uploadtype));
/*      
        $info = xarModAPIFunc('uploads',
                              'user',
                              'upload',
                                array('uploadfile'=>$inputfield,
                                          'mod'=>'uploads',
                                                'modid'=>$modid,
                                                'utype'=>$uploadtype,
                                                'extensions'=>$extensions));
*/
    if ($uploadtype == 'text') 
    {                                                         
        return $info;
    } else {
        xarResponseRedirect(xarModURL('uploads', 'admin', 'view'));
    }
}

?>