<?php

function filemanager_user_file_selector( $args=array() )
{

    extract($args);
    xarModAPILoad('filemanager', 'user');

    if (!xarVarFetch('vpath',    'str:7:', $data['path'],       '/fsroot/', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('error',    'array',  $error,              NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sortby',   'str:1:', $data['sortby'],     _FILEMANAGER_VDIR_SORTBY_NAME, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortdir',  'str:1:', $data['sortdir'],    _FILEMANAGER_VDIR_SORT_ASC,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('prefix',   'str:1:', $data['prefix'],     ''  ,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tab',      'enum:browser:uploadfile:importfile:help', $tab, 'browser', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('action',   'enum:delsel:addsel:info', $data['action'], '', XARVAR_NOT_REQUIRED)) return;

    if (isset($error) && is_array($error) && isset($error['message'])) {
        $data['error'] = $error;
        echo xarTplModule('filemanager', 'user', 'file_browser_error', $data, NULL);
        exit();
    }

    $data['pathInfo'] = xarModAPIFunc('filemanager', 'vdir', 'path_decode', array('path' => $data['path']));
    if (is_array($data['pathInfo'])) {
        extract($data['pathInfo']);
    } else {
        $data['dirId']      = 0;
        $data['fileId']     = 0;
        $data['is_mounted'] = FALSE;
        $data['is_cached']  = FALSE;
        $data['is_dir']     = FALSE;
        $data['is_file']    = FALSE;
    }

    if (isset($data['fileId']) && !empty($data['fileId'])) {
        $data['path'] = dirname($data['path']);
    }

    $data['linkInfo']['func'] = 'file_selector';
    $data['linkInfo']['args']['vpath'] = $data['path'];
    if (isset($data['prefix']) && !empty($data['prefix'])) {
        $data['linkInfo']['args']['prefix'] = $data['prefix'];
    } else {
        $data['prefix'] = '';
    }



    // this needs to be done for all 4 scenarios below
    $data['linkInfo']['args']['tab'] = $tab;
    $data['tab'] = $tab;

    // because of the extracted data[pathInfo], current directory ID is
    // in $dirId
    if (!empty($dirId)) {
        $data['path'] = xarModAPIFunc('filemanager', 'vdir', 'path_encode', array('vdir_id' => $dirId));
    }

    $data['dirList'] = xarModAPIFunc('filemanager', 'user', 'get_dir_list',
                              array('path'     => $data['path'],
                                    'linkInfo' => $data['linkInfo']));

    // change the listingfunction to be file_selector so the directory
    // browser will link back to us instead of the default, itself (directorybrowser)
    $data['listingfunction']="file_selector";

    // get the html for current paths directory browser to display in the browser
    $data['directorybrowser'] = xarModFunc('filemanager','user','directorybrowser',$data);

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


    // define all our tab options
    $tabs['browser']['label']         = xarML('Browser');
    $tabs['uploadfile']['label']      = xarML('Upload File(s)');
    $tabs['importfile']['label']      = xarML('Import File(s)');
    $tabs['help']['label']            = xarML('Help');

    // loop through tabs to further define options
    foreach($tabs as $key => $_tabTemp) {
        // set the tab to the current key so that the URL will point to it instead of the current one (defautl = browser);
        $linkInfo['args']['tab']=$key;

        // pass all current args (linkInfo[args]) into xarModURL and assign it to an array which we'll use to navigate
        // w/i the attachment manager
        $tabs[$key]['link'] = xarModURL('filemanager', 'user', 'file_selector', $linkInfo['args']);
    }

    // create an array of all the tabs we want have the template show
    $data['tabs'] = $tabs;

    // prep the link to be clicked when they're done w/ the pop up window
    $data['finished'] = xarModURL('filemanager', 'user', 'file_selector', $data['linkInfo']['args']);

    //. make the call for this specific tab
    $data['taboutput'] = xarModFunc('filemanager', 'user', $tab, $data);
    if (!is_string($data['taboutput'])) { return; }

    echo xarTplModule('filemanager','user','file_selector', $data, $tab);
    exit();
    //return $data;

}
?>