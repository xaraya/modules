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
 *  @param   string $exclude   Regex string to exclude directories
 *  @return  the multidimensional array-list of directories
 *  @returns integer The id of the file that was associated, FALSE with exception on error
 */


function filemanager_fsapi_get_dir_list($args)
{
    static $level       = 1;         // Current level node we are on in $pathDest
    static $exclude     = '';        // Exclude folders that match this
    static $vdir_path   = '';        // path to mount point
    static $vdir_id     = 0;         // ID of the virtual directory mount point
    static $path        = array();   // array of nodes showing us the path we are to follow
    static $curPath     = array();   // Directory stack
    static $linkList    = array();   // List of previously visiting symlinks (helps prevent recursion)
           $list        = array();   // our final list of directories

    $pathRoot           = NULL;     // The starting path from which we will start grabbing our directory listing
    $pathDest           = NULL;     // The relative path starting from the pathroot to the destination directory
    $linkInfo           = NULL;     // Used to create each item's link
    $mountId            = NULL;     // Used to determine the mount point path
    $exglob             = NULL;     // ...?
    $exregex            = NULL;     // Regular expression used to exclude certain results from the listing
    
    extract ($args);

    if (!isset($pathRoot)) {
        return array();
    }

    if (!isset($linkInfo['func'])) {
        $linkInfo['func'] = 'file_browser';
    }

    if (!isset($linkInfo['args'])) {
        $linkInfo['args'] = array();
    }

    if (isset($pathDest)) {
        $path = xarModAPIFunc('filemanager', 'vdir', 'split_path', array('path' => $pathDest));
        if (!count($path)) {
            $path[1] = '';
        } else {
            $path[] = "";
        }

        if (!isset($mountId)) {

            $mountpoints = @unserialize(xarModGetVar('filemanager', 'mountpoints'));
            if (is_array($mountpoints) && in_array($pathRoot, $mountpoints)) {
                $vdir_id = array_search($pathRoot, $mountpoints);
            } else {
                $vdir_id = 0;
            }


            $mountopts = @unserialize(xarModGetVar('filemanager', 'mountopts'));
            if (is_array($mountopts) && in_array($vdir_id, array_keys($mountopts))) {
                if (isset($mountopts[$vdir_id]['exclude'])) {
                    $exclude = $mountopts[$vdir_id]['exclude'];
                }
            }
        } else {
            $vdir_id = $mountId;
        }

        if (!empty($vdir_id)) {
            $vdir_path = xarModAPIFunc('filemanager', 'vdir', 'path_encode', array('vdir_id' => $vdir_id));
            $vdir_path .= '/';
        }
    }

    if (isset($exglob) && is_string($exregex)) {
        $exclude = $exregex;
    }

    // The path to a directory should always end with a '/' - make sure it does and add the current
    // path to our Directory Stack
    $curPath[] = ($pathRoot{strlen($pathRoot) - 1} == '/') ? $pathRoot : $pathRoot . '/';
    $thisPath  = $curPath[count($curPath) - 1];
    $relPath   = str_replace($curPath[0], '', $thisPath);

    if (!is_readable($pathRoot)) {
        $msg = xarML('Xaraya does not have access to read this directory...');
        xarErrorSet(XAR_USER_EXCEPTION, 'FILEMANAGER_FOLDER_UNREADABLE', new SystemException($msg));
        return NULL;
    }

    if (is_dir($pathRoot) && ($dirHandle = opendir($pathRoot))) {
        while (($file = readdir($dirHandle)) !== false) {

            $realpath = $thisPath.$file;

            if (is_dir($realpath) || is_link($realpath)) {

                // if we have an exclude string, then exclude :-)
                if (!empty($exclude)) {
                    if (eregi($exclude, $realpath)) {
                        continue;
                    }
                }

                // to avoid recursion, we get rid of . and ..
                if ($file == '.' || $file == '..') continue;

                // Check if readlink function exists - if it does, it means we're most likely
                // on a system that uses symlinks (ie: not windows). If that's the case, then
                // we need to make sure that we aren't doing any infinite recursion stuff here
                // So, dereference a link until we find it's actual destination (one that isn't
                // a link) and add it to our linkList array to be used later in our recursion checks.
                if (function_exists('readlink')) {
                    $linkpath = $realpath;
                    if (is_link($linkpath)) {

                        do {
                            $linkpath = readlink($linkpath);
                        } while (is_link($linkpath));

                        // Again, we don't want to experience recursion, so don't add links
                        // that point back to the current or previous directory
                        if (!is_dir($linkpath) || $linkpath == '.' || $linkpath == '..') {
                            continue;
                        }
                    }
                }

                // If the path is a link, then then check to see if it's destination
                // points to a location that is in our linklist - if it is, then skip
                // adding this link - otherwise, add the destination location to our
                // linkList and add this link to the directory list
                if (is_link($realpath)) {
                    $linkPath = readlink($realpath);
                    if (isset($linkList[$linkPath])) {
                        continue;
                    } else {
                        $linkList[$linkPath] = TRUE;
                    }
                }

                $list[$file]['name'] = $file;
                $list[$file]['is_mount_point'] = 0;

                $func  =& $linkInfo['func'];
                $largs =& $linkInfo['args'];
                $largs['vpath'] = $vdir_path . $relPath . $file;

                $list[$file]['link'] = xarModURL('filemanager', 'user', $func, $largs);
                // Only descend into directories that are in our $pathDest
                // Otherwise, just add the directory to our list and continue on...
                if ($file == $path[$level]) {
                    $level++;
                    $children = filemanager_fsapi_get_dir_list(array('pathRoot' => $realpath . '/', 'linkInfo' => $linkInfo));
                    $level--;

                    if (!isset($children)) {
                        return NULL;
                    }

                    $list[$file]['children'] = $children;
                    $list[$file]['selected'] = 1;
                } else {
                    $list[$file]['children'] = array();;
                    $list[$file]['selected'] = 0;
                }

            }
        }
    }
    // Leaving the current directory so pop it off our directory stack
    array_pop($curPath);

    return $list;
}

?>
