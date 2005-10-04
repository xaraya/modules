<?php

function filemanager_userapi_prepare_imports( $args )
{

    extract ($args);

    if (!isset($importFrom)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'importFrom','prepare_imports','filemanager');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($import_directory)) {
        $import_directory = xarModGetVar('filemanager', 'path.untrust');
    }

    /**
    * if the importFrom is an url, then
    * we can't descend (obviously) so set it to FALSE
    */
    if (!isset($descend)) {
        if (eregi('^(http[s]?|ftp)?\:\/\/', $importFrom)) {
            $descend = FALSE;
        } else {
            $descend = TRUE;
        }
    }

    $imports = xarModAPIFunc('filemanager','user','import_get_filelist',
                              array('fileLocation'  => $importFrom,
                                    'descend'       => $descend));
    if ($imports) {

        $imports = xarModAPIFunc('filemanager','user','import_prepare_files',
                                  array('fileList'  => $imports,
                                        'savePath'  => $import_directory,
                                        'obfuscate' => $import_obfuscate));
    } else {
        $fileInfo['errors']   = array();
        $fileInfo['fileName'] = $importFrom;
        $fileInfo['fileSrc']  = $importFrom;
        $fileInfo['fileDest'] = $import_directory;
        $fileInfo['fileSize'] = 0;

        if (xarCurrentError() !== XAR_NO_EXCEPTION) {

            while (xarCurrentErrorType() !== XAR_NO_EXCEPTION) {

                $errorObj = xarCurrentError();

                if (is_object($errorObj)) {
                    $fileError = array('errorMesg'   => $errorObj->getShort(),
                                       'errorId'    => $errorObj->getID());
                } else {
                    $fileError = array('errorMesg'   => 'Unknown Error!',
                                       'errorId'    => _FILEMANAGER_ERROR_UNKNOWN);
                }

                if (!isset($fileInfo['errors'])) {
                    $fileInfo['errors'] = array();
                }
                $fileInfo['errors'][] = $fileError;
                // Clear the exception because we've handled it already
                xarErrorHandled();
            }
        } else {
            $fileInfo['errors'][]['errorMsg'] = xarML('Unknown');
            $fileInfo['errors'][]['errorId']  = _FILEMANAGER_ERROR_UNKNOWN;
        }
        return array($fileInfo);
    }

    return $imports;
}

?>