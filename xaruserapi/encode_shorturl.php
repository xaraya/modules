<?php

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the Example module development team
 * @param $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */


function uploads_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'uploads';

    switch(strtolower($func)) {
        case 'download':
            // if we don't have a fileId, can't do too much
            if (!isset($fileId) || empty($fileId) && (!isset($vdir_path) || empty($vdir_path))) {
                break;
            } else {
                $path = xarModAPIFunc('uploads', 'vdir', 'get_file_path', array('fileId' => $fileId));
            }

            if (!empty($fileId) && is_numeric($fileId)) {
                $path = '/' . $module . $path;
            }
            break;
        case 'file_browser':
            if (isset($vpath) && is_string($vpath) && !empty($vpath)) {
                $vdir_path = $vpath;
            } elseif (isset($vdir_id) && is_numeric($vdir_id) && !empty($vdir_id)) {
                $vdir_path = xarModAPIFunc('uploads', 'vdir', 'path_encode', array('vdir_id' => $vdir_id));
            }

            if (!empty($vdir_path)) {
                $path = '/' . $module . $vdir_path;
            }
            break;
        default:
            break;
    }

    return $path;
}

?>