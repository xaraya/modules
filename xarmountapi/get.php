<?php

function filemanager_mountapi_get( $args )
{
    $vdir_id = NULL;
    
    extract($args);
    
    if (!isset($vdir_id) || empty($vdir_id)) {
        $msg = xarML('Missing or empty parameter #(1) for module #(2) function #(3)', 
                     'vdir_id', 'filemanager', 'mountapi_get');
        xarLogMessage($msg, XARLOG_LEVEL_ERROR);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    } else {
        $mountPoints = @unserialize(xarModGetVar('filemanager', 'mount.list'));
        
        if (!isset($mountPoints[$vdir_id])) {
            return FALSE;
        } else {
            return $mountPoints[$vdir_id];
        }
    }
}

?>