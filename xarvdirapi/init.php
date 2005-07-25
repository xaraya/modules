<?php

/**
 * Sets up the base directory structure and is used to recreate base directories
 * lost via some external mechanism (ie: a user deleting a base directory category).
 * Note: The directory will not be created if it already exists.
 *
 * initLevels:
 *      _UPLOADS_VDIR_ROOTFS (1):    initializes only the rootfs directory
 *      _UPLOADS_VDIR_PUBLIC (2):    initializes only the public directory
 *      _UPLOADS_VDIR_USERS  (4):    initializes only the users  directory
 *      _UPLOADS_VDIR_TRASH  (8):    initializes only the trash  directory
 *      _UPLOADS_VDIR_ALL    (15):   initializes all the base directories
 *
 * @author  Carl P. Corliss (ccorliss@schwabfoundation.org)
 * @param   integer  $initLevel bitwise field describing which portions of the fs to initialize
 * @returns bool
 * @return  TRUE on success, FALSE otherwise
 */

function uploads_vdirapi_init( $args )
{

    extract($args);

    if (!isset($initLevel) || ($initLevel > 15 || $initLevel < 1)) {
        return FALSE;
    }

    $rootfs = xarModGetVar('uploads', 'folders.rootfs');

    // Check to make sure we have a rootfs to work with
    // If we don't, we need to be sure to create it
    $missing_rootfs = TRUE;
    if (isset($rootfs) && !empty($rootfs)) {

        $check = xarModAPIFunc('categories', 'user', 'cid2name', array('cid' => $rootfs));

        if (isset($check) && !empty($check)) {
            $missing_rootfs = FALSE;
        }
    }

    /**
     * Set up the root node for the filesystem
     */
    if ($initLevel & _UPLOADS_VDIR_ROOTFS || $missing_rootfs) {
        $rootfs  = xarModAPIFunc('categories','admin','create',
                                    array('name' => xarML('fsroot'),
                                        'description' => xarML('Filesystem Root'),
                                        'parent_id' => 0));
        xarModSetVar('uploads', 'number_of_categories', 1);
        xarModSetVar('uploads', 'mastercids', $rootfs);
        xarModSetVar('uploads', 'folders.rootfs', $rootfs);
    }

    /**
     *  Set up the Public share directory
     */
    if ($initLevel & _UPLOADS_VDIR_PUBLIC) {

        $dirCheck = xarModGetVar('uploads', 'folders.public-files');
        $directory_missing = TRUE;

        if (isset($dirCheck) && !empty($dirCheck)) {

            $check = xarModAPIFunc('categories', 'user', 'cid2name', array('cid' => $dirCheck));

            if (isset($check) && !empty($check)) {
                $directory_missing = FALSE;
            }
        }

        if ($directory_missing) {
            $pubFilesID  = xarModAPIFunc('categories','admin', 'create',
                                        array('name'        => xarML('Public Files'),
                                                'description'   => xarML("Public shared files and folders."),
                                                'parent_id'     => $rootfs));

            xarModSetVar('uploads', 'folders.public-files', $pubFilesID);
        }

    }


    if ($initLevel & _UPLOADS_VDIR_USERS) {

        $dirCheck = xarModGetVar('uploads', 'folders.users');
        $directory_missing = TRUE;

        if (isset($dirCheck) && !empty($dirCheck)) {

            $check = xarModAPIFunc('categories', 'user', 'cid2name', array('cid' => $dirCheck));

            if (isset($check) && !empty($check)) {
                $directory_missing = FALSE;
            }
        }


        if ($directory_missing) {
            $userFilesID = xarModAPIFunc('categories','admin', 'create',
                                        array('name'        => xarML("Users"),
                                                'description'   => xarML("User's files and folders"),
                                                'parent_id'     => $rootfs));
            xarModSetVar('uploads', 'folders.users', $userFilesID);
        }
    }

    if ($initLevel & _UPLOADS_VDIR_TRASH) {

        $dirCheck = xarModGetVar('uploads', 'folders.trash');
        $directory_missing = TRUE;

        if (isset($dirCheck) && !empty($dirCheck)) {

            $check = xarModAPIFunc('categories', 'user', 'cid2name', array('cid' => $dirCheck));

            if (isset($check) && !empty($check)) {
                $directory_missing = FALSE;
            }
        }


        if ($directory_missing) {
            $delFilesID  = xarModAPIFunc('categories','admin', 'create',
                                        array('name'        => xarML("Recycle Bin"),
                                                'description'   => xarML("Deleted files and folders"),
                                                'parent_id'     => $rootfs));
            xarmodSetVar('uploads', 'folders.trash', $delFilesID);
        }
    }


    return TRUE;
}

?>
