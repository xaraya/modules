<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
function uploads_user_download()
{
    if (!xarSecurityCheck('ViewUploads')) return;

    if (!xarVarFetch('fileId', 'int:1:', $fileId)) return;

    $fileInfo = xarModAPIFunc('uploads','user','db_get_file', array('fileId' => $fileId));

    if (empty($fileInfo) || !count($fileInfo)) {
        xarResponseRedirect('modules/uploads/xarimages/notapproved.gif');
        return TRUE;
    //    $msg = xarML('Unable to retrieve information on file [#(1)]', $fileId);
    //    xarErrorSet(XAR_USER_EXCEPTION, 'UPLOADS_ERR_NO_FILE', new SystemException($msg));
    //    return;
    }

    // the file should be the first indice in the array
    $fileInfo = end($fileInfo);

    $instance[0] = $fileInfo['fileTypeInfo']['typeId'];
    $instance[1] = $fileInfo['fileTypeInfo']['subtypeId'];
    $instance[2] = xarSessionGetVar('uid');
    $instance[3] = $fileId;

    $instance = implode(':', $instance);

    // If you are an administrator OR the file is approved, continue
    if ($fileInfo['fileStatus'] != _UPLOADS_STATUS_APPROVED && !xarSecurityCheck('EditUploads', 0, 'File', $instance)) {
        xarErrorHandled();
        $msg = xarML('You do not have the necessary permissions for this object.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new DefaultUserException($msg));
        // No access - so return the exception
        return;
    }

    if (xarSecurityCheck('ViewUploads', 1, 'File', $instance)) {
        if ($fileInfo['storeType'] & _UPLOADS_STORE_FILESYSTEM || ($fileInfo['storeType'] == _UPLOADS_STORE_DB_ENTRY)) {
            if (!file_exists($fileInfo['fileLocation'])) {
                $msg = xarML('File [#(1)] does not exist in FileSystem.', $fileInfo['fileName']);
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_ERR_NO_FILE', new DefaultUserException($msg));
                return;
            }
        } elseif ($fileInfo['storeType'] & _UPLOADS_STORE_DB_FULL) {
            if (!xarModAPIFunc('uploads', 'user', 'db_count_data', array('fileId' => $fileInfo['fileId']))) {
                $msg = xarML('File [#(1)] does not exist in Database.', $fileInfo['fileName']);
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_ERR_NO_FILE', new DefaultUserException($msg));
                return;
            }
        }

        $result = xarModAPIFunc('uploads', 'user', 'file_push', $fileInfo);

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
                         array('module'    => 'uploads',
                               'itemtype'  => 1, // Files
                               'returnurl' => xarModURL('uploads', 'user', 'download', array('fileId' => $fileId))));

        // File has been pushed to the client, now shut down.
        exit();

    } else {
        return FALSE;
    }
}
?>
