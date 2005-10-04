<?php

function filemanager_user_directorybrowser($args)
{
     extract($args);

     if (!xarVarFetch('vpath',            'str:7:', $data['path']             ,'/fsroot/'        ,XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('listingfunction', 'str:0',  $listingfunction ,'directorybrowser',XARVAR_NOT_REQUIRED)) return;

     $data['linkInfo']['func']=$listingfunction;

     $data['dirList'] = xarModAPIFunc('filemanager', 'user', 'get_dir_list',
                               array('path'     => $data['path'],
                                     'linkInfo' => $data['linkInfo']));

     // If dirList is NULL, we have an error
     if (!isset($data['dirList']) || empty($data['dirList'])) {
         if (xarCurrentErrorType() !== XAR_NO_EXCEPTION) {
             $error = xarCurrentError();
             $data['error']['message'] = $error->msg;
             echo xarTplModule('filemanager', 'user', 'file_browser_error', $data, NULL);
             exit();
         } else {
             $error = xarCurrentError();
             $data['error'] = xarML('unknown error retrieving directory list...');
             echo xarTplModule('filemanager', 'user', 'file_browser_error', $data, NULL);
             exit();
         }
     }

    return $data;
 }

 ?>
