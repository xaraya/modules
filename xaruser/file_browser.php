<?php

function filemanager_user_file_browser( $args=array() )
{

    extract($args);

    xarModAPILoad('filemanager', 'user');

    if (!xarVarFetch('vpath',     'str:7:', $path,    '/fsroot/', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('error',     'array',  $error,   NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sortby',    'str:1:', $sortby,  _FILEMANAGER_VDIR_SORTBY_NAME, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortdir',   'str:1:', $sortdir, _FILEMANAGER_VDIR_SORT_ASC,   XARVAR_NOT_REQUIRED)) return;


    if (isset($error) && is_array($error) && isset($error['message'])) {
        $data['error'] = $error;
        echo xarTplModule('filemanager', 'user', 'file_browser_error', $data, NULL);
        exit();
    }

    $dirList = xarModAPIFunc('filemanager', 'user', 'get_dir_list', array('path' => $path));

    // If dirList is NULL, we have an error
    if (!isset($dirList) || empty($dirList)) {
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

    $fileList = xarModAPIFunc('filemanager', 'user', 'get_file_list',
                               array('path'     => $path,
                                     'sortby'   => $sortby,
                                     'sortdir'  => $sortdir));

    // If fileList is NULL, we have an error
    if (!isset($fileList)) {
        return;
    }

    $data['fileList'] = $fileList;
    $data['dirList']  = $dirList;
    $data['path']     = ($path{0} != '/') ? '/' . $path : $path;

    echo xarTplModule('filemanager','user','file_browser', $data, NULL);
    exit();
}

?>