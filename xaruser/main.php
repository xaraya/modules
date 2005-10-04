<?php

/*FUNC*/function filemanager_user_main()
{
  //this is a default page that will show the status of the item requested.
    
    // Security check
    if (!xarSecurityCheck('ViewFileManager')) return;
    
    $ulid = xarVarCleanFromInput('ulid');
    $ulname = xarVarCleanFromInput('ulname');
    
    $thumbwidth  = xarVarCleanFromInput('thumbwidth');
    $thumbheight = xarVarCleanFromInput('thumbheight');
    $thumb = xarVarCleanFromInput('thumb');
    
    //Download the file to the user
    return xarModAPIFunc('filemanager',
                          'admin',
                          'download',
                          array('ulid'=>$ulid
                                  , 'ulname'=>$ulname
                                  , 'thumbwidth'=>$thumbwidth
                                  , 'thumbheight'=>$thumbheight
                                  , 'thumb'=>$thumb
                                ));
     
    
}
?>