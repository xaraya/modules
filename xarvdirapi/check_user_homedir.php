<?php

function filemanager_vdirapi_check_user_homedir($args)
{

    extract($args);

    $uid   = xarUserGetVar('uid');
    $uname = xarUserGetVar('uname');

    $usersDirectory = xarModGetVar('filemanager', 'folders.users');
    $homedirList = xarModAPIFunc('categories', 'user', 'getcat',
                                  array('cid'           => $usersDirectory,
                                        'return_itself' => FALSE,
                                        'getchildren'   => TRUE));

    foreach ($homedirList as $homedir) {
        if ($uid == $homedir['name']) {
            return $homedir['cid'];
        }
    }

    // If we've made it this far, then the user doesn't have a home directory yet.
    $homeDirId =  xarModAPIFunc('filemanager', 'vdir', 'create',
                          array('name'         => $uid,
                                'parentid'     => $usersDirectory,
                                'description' => xarML('#(1)\'s home directory', $uname)));

    if (isset($homeDirId) && !empty($homeDirId)) {
        // Store a reference to the user's home directory   
        xarModSetUserVar('filemanager', 'folders.home', $homeDirId);

        // Create the user's Public Files directory
        xarModAPIFunc('filemanager', 'vdir', 'create', 
                array('name'        => xarML('#(1)\'s Public Files', xarUserGetVar('uname')),
                      'parentid'    => $homeDirId,
                      'description' => xarML('#(1)\'s publicly viewable files', xarUserGetVar('uname'))));
        return $homeDirId;
    } else {    
        return 0;
    }
}

?>