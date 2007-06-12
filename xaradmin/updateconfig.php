<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 * Update the configuration
 * @return bool
 */
function uploads_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('file',   'list:str:1:', $file,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('path',   'list:str:1:', $path,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('view',   'list:str:1:', $view,   '', XARVAR_NOT_REQUIRED)) return;
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
            // a real uploads module variable
            if (NULL !== xarModGetVar('uploads', 'file.'.$varname)) {
                xarModSetVar('uploads', 'file.' . $varname, $value);
            }
        }
    }
    if (isset($path) && is_array($path)) {
        foreach ($path as $varname => $value) {
            // check to make sure that the value passed in is
            // a real uploads module variable
            $value = trim(ereg_replace('\/$', '', $value));
            if (NULL !== xarModGetVar('uploads', 'path.' . $varname)) {
                if (!file_exists($value) || !is_dir($value)) {
                    $msg = xarML('Location [#(1)] either does not exist or is not a valid directory!', $value);
                    throw new BadParameterException(null,$msg);
                } elseif (!is_writable($value)) {
                    $msg = xarML('Location [#(1)] can not be written to - please check permissions and try again!', $value);
                    throw new BadParameterException(null,$msg);
                } else {
                    xarModSetVar('uploads', 'path.' . $varname, $value);
                }
            }
        }
    }
    if (isset($view) && is_array($view)) {
        foreach ($view as $varname => $value) {
            // check to make sure that the value passed in is
            // a real uploads module variable
// TODO: add other view.* variables later ?
            if ($varname != 'itemsperpage') continue;
            xarModSetVar('uploads', 'view.' . $varname, $value);
        }
    }

    if (isset($ddprop['trusted'])) {
        xarModSetVar('uploads', 'dd.fileupload.trusted', 1);
    } else {
        xarModSetVar('uploads', 'dd.fileupload.trusted', 0);
    }

    if (isset($ddprop['external'])) {
        xarModSetVar('uploads', 'dd.fileupload.external', 1);
    } else {
        xarModSetVar('uploads', 'dd.fileupload.external', 0);
    }

    if (isset($ddprop['stored'])) {
        xarModSetVar('uploads', 'dd.fileupload.stored', 1);
    } else {
        xarModSetVar('uploads', 'dd.fileupload.stored', 0);
    }

    if (isset($ddprop['upload'])) {
        xarModSetVar('uploads', 'dd.fileupload.upload', 1);
    } else {
        xarModSetVar('uploads', 'dd.fileupload.upload', 0);
    }

    // FIXME: change only if the imports-directory was changed? <rabbitt>
    // Now update the 'current working imports directory' in case the
    // imports directory was changed. We do this by first deleting the modvar
    // and then recreating it to ensure that the user's version is cleared
    // xarModDelVar('uploads', 'path.imports-cwd');
    xarModSetVar('uploads', 'path.imports-cwd', xarModGetVar('uploads', 'path.imports-directory'));

    xarModCallHooks('module', 'updateconfig', 'uploads',
                    array('module'   => 'uploads',
                          'itemtype' => 1)); // Files

    xarResponseRedirect(xarModURL('uploads', 'admin', 'modifyconfig'));

    // Return
    return TRUE;
}
?>
