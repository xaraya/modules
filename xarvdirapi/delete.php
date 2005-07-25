<?php

/**
 * Delete a single directory, or group of directories - providing they are not
 * empty and are not one of the four root directories (which are protected).
 *
 * @author  Carl P. Corliss (ccorliss@schwabfoundation.org)
 * @param   integer $dirId      ID of directory to remove
 * @param   array   $dirIds     IDs of directories to remove
 * @returns bool
 * @return  TRUE on success, FALSE otherwise
 */

function uploads_vdirapi_delete( $args )
{

    extract($args);

    if (!isset($dirId) && !isset($dirIds)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'dirId/dirIds', 'vdir_delete', 'uploads');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (isset($dirIds) && !is_array($dirIds)) {
        unset($dirIds);
    }

    if (isset($dirId) && is_numeric($dirId))  {
            $dirIds[] = $dirId;
            unset($dirId);
    }

    $protected[] = xarModGetVar('uploads', 'folders.rootfs');
    $protected[] = xarModGetVar('uploads', 'folders.public-files');
    $protected[] = xarModGetVar('uploads', 'folders.users');
    $protected[] = xarModGetVar('uploads', 'folders.trash');

    // Flip the key->value pairs around so we can
    // check for protected directories
    $dirIds = array_flip($dirIds);

    foreach ($protected as $baseDir) {
        if (array_key_exists($baseDir, $dirIds)) {
            unset($dirIds[$baseDir]);
        }
    }

    $dirIds = array_flip($dirIds);

    /**

        Grab a complete directory list of all affected files/folders
        unlink each file
        delete each folder (preferably narrow the folders down to the roots

    */


}

?>
