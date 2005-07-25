<?php

/**
 * returns the virtual directory path to the folder containing the specified file
 *
 * @param   integer $fildId      ID of the file who's path we want
 * @returns string
 * @return  the path to the directory containing the specified file, or FALSE on error
 */

function uploads_vdirapi_get_file_path( $args )
{

    extract($args);

    if (!isset($fileId) || empty($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'fileId', 'vdir_get_filepath', 'uploads');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    $uri     = xarModAPIFunc('uploads', 'user', 'db_get_file_location', array('fileId' => $fileId));

    $pathInfo = parse_url($uri);

    if (!isset($pathInfo) || empty($pathInfo)) {
        return FALSE;
    } else {
        if (!isset($pathInfo['scheme']) || empty($pathInfo['scheme'])) {
                $pathInfo['scheme'] = '';
        }
        switch (strtolower($pathInfo['scheme'])) {
            case 'mount':

                $dirPath = xarModAPIFunc('uploads', 'vdir', 'path_encode',
                                          array('vdir_id' => $pathInfo['host']));
                return $dirPath . '/' . (($pathInfo['path']{0} == '/') ? substr($pathInfo['path'], 1) : $pathInfo['path']);
                break;
            case 'xarfs':
                $fileName = xarModAPIFunc('uploads', 'user', 'db_get_filename', array('fileId' => $fileId));
                $dirPath = xarModAPIFunc('uploads', 'vdir', 'path_encode',
                                          array('vdir_id' => $pathInfo['host']));
                return $dirPath . '/' . $fileName;
                break;
            default:
                return '';
                break;
        }
         
    }
}

?>
