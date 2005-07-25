<?php

function uploads_fsapi_in_trash($args)
{
    extract($args);

    if (!isset($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for API function [#(2)] in module (#3)]',
                     'fileId','db_modify_file','uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }


    $trash = xarModGetVar('uploads','folders.trash');

    $file = xarModAPIFunc('uploads', 'user', 'db_get_associations',
                           array('modid'  => xarModGetIdFromName('categories'),
                                 'itemid' => $trash,
                                 'itemtype' => 0,
                                 'fileid' => $fileId));

    if (count($file)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

?>
