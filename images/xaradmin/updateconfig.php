<?php

function images_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('libtype', 'list:int:1:3', $libtype,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('path',    'list:str:1:',  $path,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shortURLs', 'checkbox', $shortURLs, TRUE)) return;

    if (isset($shortURLs) && $shortURLs) {
        xarModSetVar('images', 'SupportShortURLs', TRUE);
    } else {
        xarModSetVar('images', 'SupportShortURLs', FALSE);
    }

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (isset($libtype) && is_array($libtype)) {
        foreach ($libtype as $varname => $value) {
            // check to make sure that the value passed in is
            // a real images module variable
            if (NULL !== xarModGetVar('images', 'type.'.$varname)) {
                xarModSetVar('images', 'type.' . $varname, $value);
            }
        }
    }
    if (isset($path) && is_array($path)) {
        foreach ($path as $varname => $value) {
            // check to make sure that the value passed in is
            // a real images module variable
            $value = trim(ereg_replace('\/$', '', $value));
            if (NULL !== xarModGetVar('images', 'path.' . $varname)) {
                if (!file_exists($value) || !is_dir($value)) {
                    $msg = xarML('Location [#(1)] either does not exist or is not a valid directory!', $value);
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'INVALID_DIRECTORY', new SystemException($msg));
                    return;
                } elseif (!is_writable($value)) {
                    $msg = xarML('Location [#(1)] can not be written to - please check permissions and try again!', $value);
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NOT_WRITABLE', new SystemException($msg));
                    return;
                } else {
                    xarModSetVar('images', 'path.' . $varname, $value);
                }
            }
        }
    }

    xarModCallHooks('module', 'updateconfig', 'images', array('module' => 'images'));
    xarResponseRedirect(xarModURL('images', 'admin', 'modifyconfig'));

    // Return
    return TRUE;
}
?>
