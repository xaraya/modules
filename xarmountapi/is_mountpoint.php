<?php

function filemanager_mountapi_is_mountpoint( $args )
{
    $path    = NULL;
    $vdir_id = NULL;
    
    extract($args);

    if ((!isset($path) || empty($path)) && (!isset($vdir_id) || empty($vdir_id))) {
        // If we didn't recv a valid path or vdir_id, then we can 
        // definitely say that it's not a mount point ;-)
        return FALSE;
    } else {
        $mountPoints = @unserialize(xarModGetVar('filemanager', 'mount.list'));
        
        if (isset($vdir_id) && !empty($vdir_id)) {
            if (in_array($vdir_id, array_keys($mountPoints))) {
                return $vdir_id;
            }
        } elseif (isset($path) && !empty($path)) {
            foreach ($mountPoints as $dirId => $mountInfo) {
                if (stristr($path, $mountInfo['path'])) {
                    return $dirId;
                }
            } 
        }
    }
    
    return FALSE;
}

?>