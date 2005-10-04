<?php

// FIXME: <rabbitt> only allow download of files that are -approved

function filemanager_user_download()
{
    if (!xarSecurityCheck('ViewFileManager')) return;

    if (!xarVarFetch('fileId', 'int:1:', $fileId, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('vpath', 'str:1:',  $vpath,  NULL, XARVAR_NOT_REQUIRED)) return;

    if ( (!isset($fileId) || empty($fileId)) && (!isset($vpath) || empty($vpath))) {
        $msg = xarML('Missing both parameter [#(1)] and [#(2)] for function [#(3)] in module [#(4)]. One or the other is required for successful operation.',
                     'fileId','vpath', 'db_get_file_data','filemanager');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($fileId)) {
        $vpathInfo = xarModAPIFunc('filemanager', 'vdir', 'path_decode', array('path' => $vpath));
        if (!isset($vpathInfo['fileId'])) {
            $msg = xarML('The path: [#(1)] is invalid or does not exist.', $vpath);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return FALSE;
        } else {
            $fileId = $vpathInfo['fileId'];
        }
    }


    $fileInfo = xarModAPIFunc('filemanager','user','db_get_file', array('fileId' => $fileId));

    if (empty($fileInfo) || !count($fileInfo)) {
        $msg = xarML('Unable to retrieve information on file [#(1)]', $fileId);
        xarErrorSet(XAR_USER_EXCEPTION, 'FILEMANAGER_ERR_NO_FILE', new SystemException($msg));
        return;
    }

    // the file should be the first indice in the array
    $fileInfo = end($fileInfo);

    $instance[0] = $fileInfo['fileTypeInfo']['typeId'];
    $instance[1] = $fileInfo['fileTypeInfo']['subtypeId'];
    $instance[2] = xarSessionGetVar('uid');
    $instance[3] = $fileId;

    $instance = implode(':', $instance);

    // If you are an administrator OR the file is approved, continue
    if ($fileInfo['fileStatus'] != _FILEMANAGER_STATUS_APPROVED && !xarSecurityCheck('AdminFileManager', false, 'File' . $instance)) {
        xarErrorHandled();
        $msg = xarML('You do not have the necessary permissions for this object.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new DefaultUserException($msg));
        // No access - so return the exception
        return;
    }

    if (xarSecurityCheck('ViewFileManager', 1, 'File', $instance)) {

        if ($fileInfo['storetype']['value'] & _FILEMANAGER_STORE_FILESYSTEM || ($fileInfo['storetype']['value'] == _FILEMANAGER_STORE_DB_ENTRY)) {
            if (!file_exists($fileInfo['fileLocation'])) {
                $msg = xarML('File [#(1)] does not exist in FileSystem.', $fileInfo['fileName']);
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_ERR_NO_FILE', new DefaultUserException($msg));
                return;
            }
        } elseif ($fileInfo['storetype']['value'] & _FILEMANAGER_STORE_DB_FULL) {
            if (!xarModAPIFunc('filemanager', 'user', 'db_count_data', array('fileId' => $fileInfo['fileId']))) {
                $msg = xarML('File [#(1)] does not exist in Database.', $fileInfo['fileName']);
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_ERR_NO_FILE', new DefaultUserException($msg));
                return;
            }
        }

        $result = xarModAPIFunc('filemanager', 'fs', 'push', $fileInfo);

        if (!$result || xarCurrentErrorType() !== XAR_NO_EXCEPTION) {
            // now just return and let the error bubble up
            return FALSE;
        }

        // Let any hooked modules know that we've just pushed a file
        // the hitcount module in particular needs to know to save the fact
        // that we just pushed a file and not display the count
        xarVarSetCached('Hooks.hitcount','save', 1);

        // Note: we're ignoring the output from the display hooks here
        xarModCallHooks('item', 'display', $fileId,
                         array('module'    => 'filemanager',
                               'itemtype'  => 1, // Files
                               'returnurl' => xarModURL('filemanager', 'user', 'download', array('fileId' => $fileId))));

        // File has been pushed to the client, now shut down.
        exit();

    } else {
        return FALSE;
    }
}
?>
