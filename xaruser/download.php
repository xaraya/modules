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
function uploads_user_download()
{
    if (!xarSecurityCheck('ViewUploads')) return;

    if (!xarVarFetch('file', 'str:1:', $fileName, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fileId', 'int:1:', $fileId, 0, XARVAR_NOT_REQUIRED)) return;

    $fileInfo = xarModAPIFunc('uploads','user','db_get_file', array('fileId' => $fileId));

    if (empty($fileName) && (empty($fileInfo) || !count($fileInfo))) {
        xarResponse::redirect(sys::code() . 'modules/uploads/xarimages/notapproved.gif');
        return true;
    }
    
    if (!empty($fileName)) {
    
        $fileInfo = xarSession::getVar($fileName);
        
        try {
            $result = xarModAPIFunc('uploads', 'user', 'file_push', $fileInfo);
        } catch (Exception $e) {
            return false;
        }

        // Let any hooked modules know that we've just pushed a file
        // the hitcount module in particular needs to know to save the fact
        // that we just pushed a file and not display the count
        xarVarSetCached('Hooks.hitcount','save', 1);

        // File has been pushed to the client, now shut down.
        exit();
    
    } else {
    
        // the file should be the first indice in the array
        $fileInfo = end($fileInfo);
    
        // Check whether download is permitted
        switch (xarModVars::get('uploads', 'permit_download')) {
            // No download permitted
            case 0:
                $permitted = false;
            break;
            // Personally files only
            case 1:
                $permitted = $fileInfo['userId'] == xarSession::getVar('role_id') ? true : false;
            break;
            // Group files only
            case 2:
                $rawfunction = xarModVars::get('uploads', 'permit_download_function');
                if (empty($rawfunction)) $permitted = false;
                $funcparts = explode(',',$rawfunction);
                try {
                    $permitted = xarMod::apiFunc($funcparts[0],$funcparts[1],$funcparts[2],array('fileInfo' => $fileInfo));
                } catch (Exception $e) {
                    $permitted = false;
                }
            break;
            // All files
            case 3:
                $permitted = true;
            break;
        }
        if (!$permitted) {
            return xarResponse::NotFound();
        }
        
        $instance[0] = $fileInfo['fileTypeInfo']['typeId'];
        $instance[1] = $fileInfo['fileTypeInfo']['subtypeId'];
        $instance[2] = xarSession::getVar('uid');
        $instance[3] = $fileId;
    
        $instance = implode(':', $instance);
    
        // If you are an administrator OR the file is approved, continue
        if ($fileInfo['fileStatus'] != _UPLOADS_STATUS_APPROVED && !xarSecurityCheck('EditUploads', 0, 'File', $instance)) {
            return xarTplModule('uploads','user','errors',array('layout' => 'no_permission'));
        }

        if (xarSecurityCheck('ViewUploads', 1, 'File', $instance)) {
            if ($fileInfo['storeType'] & _UPLOADS_STORE_FILESYSTEM || ($fileInfo['storeType'] == _UPLOADS_STORE_DB_ENTRY)) {
                if (!file_exists($fileInfo['fileLocation'])) {
                    return xarTplModule('uploads','user','errors',array('layout' => 'not_accessible'));
                }
            } elseif ($fileInfo['storeType'] & _UPLOADS_STORE_DB_FULL) {
                if (!xarModAPIFunc('uploads', 'user', 'db_count_data', array('fileId' => $fileInfo['fileId']))) {
                    return xarTplModule('uploads','user','errors',array('layout' => 'not_accessible'));
                }
            }
    
            $result = xarModAPIFunc('uploads', 'user', 'file_push', $fileInfo);
    
            /*
            if (!$result || xarCurrentErrorType() !== XAR_NO_EXCEPTION) {
                // now just return and let the error bubble up
                return FALSE;
            }
            */
    
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
            return false;
        }
    }
}
?>
