<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 * Update the configuration
 * @return bool
 */
function uploads_admin_updateconfig()
{
    // Get parameters
    if (!xarVar::fetch('file', 'list:str:1:', $file, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('imports_directory', 'str:1:', $imports_directory, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('uploads_directory', 'str:1:', $uploads_directory, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('view', 'list:str:1:', $view, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('ddprop', 'array:1:', $ddprop, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('permit_download', 'int', $permit_download, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('permit_download_function', 'str', $permit_download_function, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    // Confirm authorisation code.
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    xarModVars::set('uploads', 'uploads_directory', $uploads_directory);
    xarModVars::set('uploads', 'imports_directory', $imports_directory);

    xarModVars::set('uploads', 'permit_download', $permit_download);
    xarModVars::set('uploads', 'permit_download_function', $permit_download_function);

    if (isset($file) && is_array($file)) {
        foreach ($file as $varname => $value) {
            // if working on maxsize, remove all commas
            if ($varname == 'maxsize') {
                $value = str_replace(',', '', $value);
            }
            // check to make sure that the value passed in is
            // a real uploads module variable
            if (null !== xarModVars::get('uploads', 'file.'.$varname)) {
                xarModVars::set('uploads', 'file.' . $varname, $value);
            }
        }
    }

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'uploads']);
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, use_module_icons');
    $data['module_settings']->getItem();
    $isvalid = $data['module_settings']->checkInput();
    if (!$isvalid) {
        return xarTpl::module('dynamicdata', 'admin', 'modifyconfig', $data);
    } else {
        $itemid = $data['module_settings']->updateItem();
    }

    if (isset($ddprop['trusted'])) {
        xarModVars::set('uploads', 'dd.fileupload.trusted', 1);
    } else {
        xarModVars::set('uploads', 'dd.fileupload.trusted', 0);
    }

    if (isset($ddprop['external'])) {
        xarModVars::set('uploads', 'dd.fileupload.external', 1);
    } else {
        xarModVars::set('uploads', 'dd.fileupload.external', 0);
    }

    if (isset($ddprop['stored'])) {
        xarModVars::set('uploads', 'dd.fileupload.stored', 1);
    } else {
        xarModVars::set('uploads', 'dd.fileupload.stored', 0);
    }

    if (isset($ddprop['upload'])) {
        xarModVars::set('uploads', 'dd.fileupload.upload', 1);
    } else {
        xarModVars::set('uploads', 'dd.fileupload.upload', 0);
    }

    // FIXME: change only if the imports_directory was changed? <rabbitt>
    // Now update the 'current working imports directory' in case the
    // imports directory was changed. We do this by first deleting the modvar
    // and then recreating it to ensure that the user's version is cleared
    // xarModVars::delete('uploads', 'path.imports-cwd');
    xarModVars::set('uploads', 'path.imports-cwd', xarModVars::get('uploads', 'imports_directory'));

    xarModHooks::call(
        'module',
        'updateconfig',
        'uploads',
        ['module'   => 'uploads',
                          'itemtype' => 1, ]
    ); // Files

    xarController::redirect(xarController::URL('uploads', 'admin', 'modifyconfig'));

    // Return
    return true;
}
