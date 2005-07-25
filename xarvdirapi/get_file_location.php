<?php

function uploads_vdirapi_get_file_location($args)
{
    extract($args);

    if (!isset($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for API function [#(2)] in module (#3)]',
                     'fileId','vdir_get_file_location','uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($asPath)) {
        $asPath = TRUE;
    } else {
        $asPath = (bool) $asPath;
    }

    $file = xarModAPIFunc('uploads', 'user', 'db_get_associations',
                           array('modid'  => xarModGetIdFromName('categories'),
                                 'itemtype' => 0,
                                 'fileid' => $fileId));

    if (count($file)) {
        if ($asPath) {
            return xarModAPIFunc('uploads', 'vdir', 'path_encode', 
                array('vdir_id' => $file[$fileId]['itemid']));
        } else {
            return $file[$fileId]['itemid'];
        }
    } else {
        return '/unknown/';
    }
}

?>
