<?php

function filemanager_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('file',   'list:str:1:', $file,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('path',   'list:str:1:', $path,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ddprop', 'array:1:',    $ddprop, '', XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.  
    if (!xarSecConfirmAuthKey()) return;

    if (isset($file) && is_array($file)) {
        foreach ($file as $varname => $value) {
            // if working on maxsize, remove all commas
            if ($varname == 'maxsize') {
                $value = str_replace(',', '', $value);
            }
            // check to make sure that the value passed in is 
            // a real filemanager module variable
            if (NULL !== xarModGetVar('filemanager', 'file.'.$varname)) {
                xarModSetVar('filemanager', 'file.' . $varname, $value);    
            }
        }
    }
    if (isset($path) && is_array($path)) {
        foreach ($path as $varname => $value) {
            // check to make sure that the value passed in is 
            // a real filemanager module variable
            $value = trim(ereg_replace('\/$', '', $value));
            if (NULL !== xarModGetVar('filemanager', 'path.' . $varname)) {
                if (!file_exists($value) || !is_dir($value)) {
                    $msg = xarML('Location [#(1)] either does not exist or is not a valid directory!', $value);
                    xarErrorSet(XAR_USER_EXCEPTION, 'INVALID_DIRECTORY', new DefaultUserException($msg));
                    return;
                } elseif (!is_writable($value)) {
                    $msg = xarML('Location [#(1)] can not be written to - please check permissions and try again!', $value);
                    xarErrorSet(XAR_USER_EXCEPTION, 'NOT_WRITABLE', new DefaultUserException($msg));
                    return;
                } else {
                    xarModSetVar('filemanager', 'path.' . $varname, $value);
                }
            }
        }
    }

    if (isset($ddprop['trusted'])) {
        xarModSetVar('filemanager', 'dd.fileupload.trusted', 1);
    } else {
        xarModSetVar('filemanager', 'dd.fileupload.trusted', 0);
    }

    if (isset($ddprop['external'])) {
        xarModSetVar('filemanager', 'dd.fileupload.external', 1);
    } else {
        xarModSetVar('filemanager', 'dd.fileupload.external', 0);
    }

    if (isset($ddprop['stored'])) {
        xarModSetVar('filemanager', 'dd.fileupload.stored', 1);
    } else {
        xarModSetVar('filemanager', 'dd.fileupload.stored', 0);
    }

    if (isset($ddprop['upload'])) {
        xarModSetVar('filemanager', 'dd.fileupload.upload', 1);
    } else {
        xarModSetVar('filemanager', 'dd.fileupload.upload', 0);
    }
    
    // FIXME: change only if the imports-directory was changed? <rabbitt>
    // Now update the 'current working imports directory' in case the 
    // imports directory was changed. We do this by first deleting the modvar
    // and then recreating it to ensure that the user's version is cleared
    // xarModDelVar('filemanager', 'path.imports-cwd');
    xarModSetVar('filemanager', 'path.imports-cwd', xarModGetVar('filemanager', 'path.imports-directory'));

    xarModCallHooks('module', 'updateconfig', 'filemanager',
                    array('module'   => 'filemanager',
                          'itemtype' => 1)); // Files

    xarResponseRedirect(xarModURL('filemanager', 'admin', 'modifyconfig'));

    // Return
    return TRUE;
}
?>
