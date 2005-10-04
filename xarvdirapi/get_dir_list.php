<?php

/**
 *  Grabs a list of directories based on the specified $pathRoot/$pathDest including the direct children of directories,
 *  specified in the path. This will only grab the directories from the $pathRoot down to the $pathDest. So, if "/a"
 *  is your $pathRoot and your $pathDest is "c/2/b/1" and given a directory structure of:
 *  <pre>
 *      a
 *      |-- a
 *      |   |-- 1
 *      |   |-- 2
 *      |   `-- 3
 *      |-- b
 *      |   |-- 1
 *      |   |-- 2
 *      |   `-- 3
 *      `-- c
 *          |-- 1
 *          |-- 2
 *          |   |-- a
 *          |   `-- b
 *          |       `-- 1
 *          `-- 3
 *
 *  The list returned would be:
 *
 *  a
 *  b
 *  c
 *  |-- 1
 *  |-- 2
 *  |   |-- a
 *  |   `-- b
 *  |      `-- 1
 *  `--3
 *  </pre>
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string $pathDest  The relative path <em><strong>starting from</strong></em> the $pathRoot to the destination directory
 *  @param   string $pathRoot  The starting path from which we will start grabbing our directory list
 *  @return  the multidimensional array-list of directories
 *  @returns integer The id of the file that was associated, FALSE with exception on error
 */


function filemanager_vdirapi_get_dir_list( $args )
{

    extract($args);

    if (!isset($vdir_id) || empty($vdir_id) || !is_numeric($vdir_id)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'vdir_id', 'vdir_get_dir_list', 'filemanager');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($path) || empty($path) || !is_string($path)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'path', 'vdir_get_dir_list', 'filemanager');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($linkInfo['func'])) {
        $linkInfo['func'] = 'browser';
    }

    if (!isset($linkInfo['args'])) {
        $linkInfo['args'] = array();
    }

    // Ensures the user has a home directory, creating it if necessary
    $userDir = xarModAPIFunc('filemanager', 'vdir', 'check_user_homedir');

    $path = xarModAPIFunc('filemanager', 'vdir', 'split_path', array('path' => $path));

    $dirList = xarModAPIFunc('categories', 'user', 'getcat',
                              array('cid'           => xarModGetVar('filemanager', 'folders.rootfs'),
                                    'return_itself' => TRUE,
                                    'getchildren'   => TRUE));



    $dirList = array_reverse($dirList, TRUE);

    // array_reverse messes up the indice names
    // so we need to reconstruct it here
    foreach ($dirList as $key => $entry) {
        $newList[$entry['cid']] = $entry;
        $newList[$entry['cid']]['is_mount_point'] = xarModAPIFunc('filemanager', 'mount', 'is_mountpoint', 
                                                            array('vdir_id' => $entry['cid']));
    }
    $dirList = $newList;

    // Now we can create the recursive list of
    // categories within categories...
    foreach ($dirList as $key => $entry) {
        if (isset($dirList[$entry['parent']])) {
            // Every entry should have a 'children' array
            // so if it doesn't, create an empty one for it
            if (!isset($dirList[$key]['children'])) {
                $dirList[$key]['children'] = array();
            }
            $dirList[$entry['parent']]['children'][$key] = $dirList[$key];
            unset($dirList[$key]);
        }
    }

    return __filemanager_vdir_create_dir_list($dirList, $linkInfo, $path);
}

function __filemanager_vdir_create_dir_list($dirList, $linkInfo, $pathDest=NULL)
{
    static $is_users = 0;           // Whether or not this is a user's directory
    static $level    = 1;           // Current level node we are on in $pathDest
    static $path     = array();     // array of nodes showing us the path we are to follow
    static $curPath  = array();     // Directory stack
           $list     = array();     // our final list of directories
           

    $homeDirId  = xarModGetUserVar('filemanager', 'folders.home');     // the user's home directory id
    $pubDirId   = xarModGetVar('filemanager', 'folders.public-files'); // the public files directory id
    $usersDirId = xarModGetVar('filemanager', 'folders.users');        // the users directory id
    
    if (isset($pathDest)) {
        $path = $pathDest;
        // Add empty path to the end so we get the
        // children directories of the last directory
        // in the path
        $path[] = '';
    }

    foreach ($dirList as $key => $entry) {
        $last = count($list);
        // If we've descended into the Users directory, then 
        // make sure to display the users folders as names 
        // instead of their uid numbers, and display the current
        // user's folder name as 'My Files'
        if ($is_users && $homeDirId == $key) {
            $list[$entry['name']]['name'] = xarML('My Files');
        } elseif ($is_users && is_numeric($entry['name'])) {
            $list[$entry['name']]['name'] = xarUserGetVar('uname', $entry['name']);
        } else {
            $list[$entry['name']]['name'] = $entry['name'];
        }
        
        $vpath = xarModAPIFunc('filemanager', 'vdir', 'path_encode', array('vdir_id' => $entry['cid']));
        $linkInfo['args']['vpath'] = $vpath;
        $link = xarModURL('filemanager', 'user', $linkInfo['func'], $linkInfo['args']);
        $list[$entry['name']]['link'] = $link;

        if ($entry['name'] == $path[$level]) {
            // Make note of the fact that we are now in the Users folder 
            // so we can handle renaming of each user's folder name appropriately
            if ($entry['cid'] == $usersDirId) { $is_users = 1; } 
            $level++;
            $children = __filemanager_vdir_create_dir_list($entry['children'], $linkInfo);
            $level--;
            if ($entry['cid'] == $usersDirId && $is_users) { $is_users = 0; } 
            
            $list[$entry['name']]['children'] = $children;
            $list[$entry['name']]['selected'] = 1;
            $list[$entry['name']]['is_mount_point'] = $entry['is_mount_point'];
        } else {
            $list[$entry['name']]['children'] = array();;
            $list[$entry['name']]['selected'] = 0;
            $list[$entry['name']]['is_mount_point'] = $entry['is_mount_point'];
        }

    }
    return $list;
}
?>
