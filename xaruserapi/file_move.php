<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Move a file from one location to another. Can (or will eventually be able to) grab a file from
 *  a remote site via ftp/http/etc and save it locally as well. Note: isUpload=TRUE implies isLocal=True
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string  fileSrc    Complete path to source file
 *  @param   string  fileDest   Complete path to destination
 *  @param   boolean isUpload   Whether or not this file was uploaded (uses special checks on uploaded files)
 *  @param   boolean isLocal    Whether or not the file is a Local file or not (think: grabbing a web page)
 *
 *  @return boolean TRUE on success, FALSE otherwise
 */

function uploads_userapi_file_move( $args )
{

    extract ($args);

    if (!isset($force)) {
        $force = TRUE;
    }

    // if it wasn't specified, assume TRUE
    if (!isset($isUpload)) {
        $isUpload = FALSE;
    }

    if (!isset($fileSrc)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileSrc','file_move','uploads');
        throw new Exception($msg);             
    }

    if (!isset($fileDest)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'fileDest','file_move','uploads');
        throw new Exception($msg);             
    }

    if (!is_readable($fileSrc)) {
        $msg = xarML('Unable to move file - Source file [#(1)]is unreadable!', $fileSrc);
        throw new Exception($msg);             
    }

    if (!file_exists($fileSrc)) {
        $msg = xarML('Unable to move file - Source file [#(1)]does not exist!', $fileSrc);
        throw new Exception($msg);             
    }

    $dirDest = realpath(dirname($fileDest));

    if (!file_exists($dirDest))  {
        $msg = xarML('Unable to move file - Destination directory does not exist!');
        throw new Exception($msg);             
    }

    if (!is_writable($dirDest)) {
        $msg = xarML('Unable to move file - Destination directory is not writable!');
        throw new Exception($msg);             
    }

    $freespace = @disk_free_space($dirDest);
    if (!empty($freespace) && $freespace <= filesize($fileSrc)) {
        $msg = xarML('Unable to move file - Destination drive does not have enough free space!');
        throw new Exception($msg);             
    }

    if (file_exists($fileDest) && $force != TRUE) {
        $msg = xarML('Unable to move file - Destination file already exists!');
        throw new Exception($msg);             
    }

    if ($isUpload) {
        if (!move_uploaded_file($fileSrc, $fileDest)) {
            $msg = xarML('Unable to move file [#(1)] to destination [#(2)].',$fileSrc, $fileDest);
            throw new Exception($msg);             
        }
    } else {
        if (!copy($fileSrc, $fileDest)) {
            $msg = xarML('Unable to move file [#(1)] to destination [#(2)].',$fileSrc, $fileDest);
            throw new Exception($msg);             
        }
        // Now remove the file :-)
        @unlink($fileSrc);
    }

    return TRUE;
}

?>
