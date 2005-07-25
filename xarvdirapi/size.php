<?php

/**
 * Get the total size of a directory and all it's subdirectories
 *
 * @author  Carl P. Corliss (ccorliss@schwabfoundation.org)
 * @param   integer $dirId      ID of directory to get the size of
 * @returns long long
 * @return  total size of directory or FALSE on error
 */

function uploads_vdirapi_size( $args )
{

    extract($args);

    if (!isset($vdir_id)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'dirId/dirIds', 'vdir_delete', 'uploads');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;

    }
    $totalSize   = 0;
    $directories = array_keys(xarModAPIFunc('categories', 'user', 'getcat',
                                  array('cid'           => $vdir_id,
                                        'getchildren'   => TRUE,
                                        'return_itself' => TRUE,
                                        'indexby'       => 'cid')));


    // Add the appears of size for a directory
    $totalSize += count($directories) * 1024;

    foreach ($directories as $directory) {
        $fileList = array_keys(xarModAPIFunc('uploads', 'user', 'db_get_associations',
                                   array('modid'    => xarModGetIDFromName('categories'),
                                         'itemtype' => 0,
                                         'itemid'   => $directory)));

        $totalSize += xarModAPIFunc('uploads', 'user', 'db_get_filesize', array('fileId' => $fileList));
    }

    return $totalSize;
}

?>
