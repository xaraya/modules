<?php

/**
 *  Move a file from one location to another. Can (or will eventually be able to) grab a file from
 *  a remote site via ftp/http/etc and save it locally as well. Note: isUpload=TRUE implies isLocal=True
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string  source    Complete path to source file
 *  @param   string  destination   Complete path to destination
 *  @param   boolean isUpload   Whether or not this file was uploaded (uses special checks on uploaded files)
 *  @param   boolean force      Force an overwrite of the file if it already exists.
 *
 *  @returns boolean TRUE on success, FALSE otherwise
 */

function uploads_fsapi_move( $args )
{

    extract ($args);

    if (!isset($force)) {
        $force = TRUE;
    }
    
    // if it wasn't specified, assume TRUE
    if (!isset($isUpload)) {
        $isUpload = FALSE;
    }

    if (!isset($source)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'source','file_move','uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($destination)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'destination','file_move','uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!is_readable($source)) {
        $msg = xarML('Unable to move file - Source file [#(1)] is unreadable!', $source);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_READ', new SystemException($msg));
        return FALSE;
    }

    if (!file_exists($source)) {
        $msg = xarML('Unable to move file - Source file [#(1)] does not exist!', $source);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NOT_EXIST', new SystemException($msg));
        return FALSE;
    }

    $dirDest = realpath(dirname($destination));

    if (!file_exists($dirDest))  {
        $msg = xarML('Unable to move file - Destination directory does not exist!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NOT_EXIST', new SystemException($msg));
        return FALSE;
    }

    if (!is_writable($dirDest)) {
        $msg = xarML('Unable to move file - Destination directory is not writable!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_WRITE', new SystemException($msg));
        return FALSE;
    }

    if (disk_free_space($dirDest) <= filesize($source)) {
        $msg = xarML('Unable to move file - Destination drive does not have enough free space!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_SPACE', new SystemException($msg));
        return FALSE;
    }

    if (file_exists($destination) && $force != TRUE) {
        $msg = xarML('Unable to move file - Destination file already exists!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_OVERWRITE', new SystemException($msg));
        return FALSE;
    }

    if ($isUpload) {
        if (!move_uploaded_file($source, $destination)) {
            $msg = xarML('Unable to move file [#(1)] to destination [#(2)].',$source, $destination);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_MOVE', new SystemException($msg));
            return FALSE;
        }
    } else {
        if (!copy($source, $destination)) {
            $msg = xarML('Unable to move file [#(1)] to destination [#(2)].',$source, $destination);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_MOVE', new SystemException($msg));
            return FALSE;
        }
        // Now remove the file :-)
        unlink($source);
    }

    return TRUE;
}

?>
