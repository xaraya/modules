<?php

/**
 * Receives and processes delete events from other modules. Currently, it only processes
 * delete events from the categories module, and checks to see if a root directory
 * was deleted. If a root directory was deleted, it is recreated.
 *
 * Note: if deleting directories via the categories module, all previous directory structure,
 * including files->directory linkages will have been removed. All interaction with directories
 * should be done via filemanager API calls, or the filemanager GUI.
 *
 * @author Carl P. Corliss (ccorliss@schwabfoundation.org)
 * @param array $extrainfo array containing information about the deleted object
 * @returns bool
 * @return TRUE on success, FALSE on failure
 */

function filemanager_adminapi_deletehook( $args )
{

    extract($args);

    if (!isset($extrainfo) && !isset($extrainfo['module'])) {
        // If we can't get the info we want, then it isn't coming
        // from someone we care about so we should be able to
        // safely disregard and return true
        return TRUE;
    } else {

        switch(strtolower($extrainfo['module'])) {
            case 'categories':
                // Load the api just in case we need it
                xarModAPILoad('filemanager', 'user');

                $initLevel = 0;

                $rootfs = xarModGetVar('filemanager', 'folders.rootfs');
                $public = xarModGetVar('filemanager', 'folders.public-files');
                $users  = xarModGetVar('filemanager', 'folders.users');
                $trash  = xarModGetVar('filemanager', 'folders.trash');

                // Make sure that none of the base filesystem directories
                // were deleted - if they were, rebuild them
                switch ($extrainfo['cid']) {
                    case $rootfs:
                        $initLevel = _FILEMANAGER_VDIR_ALL;
                        break;
                    case $public:
                        $initLevel = _FILEMANAGER_VDIR_PUBLIC;
                        break;
                    case $users:
                        $initLevel = _FILEMANAGER_VDIR_USERS;
                        break;
                    case $trash:
                        $initLevel = _FILEMANAGER_VDIR_TRASH;
                        break;
                }

                if ($initLevel) {
                    xarModAPIFunc('filemanager', 'vdir', 'init',
                                   array('initLevel' => $initLevel));
                }

                break;
        }


    }

}

?>
