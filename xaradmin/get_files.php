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

// load defined constants
xarMod::apiLoad('uploads', 'user');

function uploads_admin_get_files()
{
    if (!xarSecurity::check('AddUploads')) {
        return;
    }

    $actionList[] = _UPLOADS_GET_UPLOAD;
    $actionList[] = _UPLOADS_GET_EXTERNAL;
    $actionList[] = _UPLOADS_GET_LOCAL;
    $actionList[] = _UPLOADS_GET_REFRESH_LOCAL;
    $actionList = 'enum:' . implode(':', $actionList);

    // What action are we performing?
    if (!xarVar::fetch('action', $actionList, $args['action'], null, xarVar::NOT_REQUIRED)) {
        return;
    }

    // StoreType can -only- be one of FSDB or DB_FULL
    $storeTypes = _UPLOADS_STORE_FSDB . ':' . _UPLOADS_STORE_DB_FULL;
    if (!xarVar::fetch('storeType', "enum:$storeTypes", $storeType, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    // now make sure someone hasn't tried to change our maxsize on us ;-)
    $file_maxsize = xarModVars::get('uploads', 'file.maxsize');

    switch ($args['action']) {
        case _UPLOADS_GET_UPLOAD:
            $uploads = DataPropertyMaster::getProperty(array('name' => 'uploads'));
            $uploads->initialization_initial_method = $args['action'];
            $uploads->checkInput('upload');
            $args['upload'] = $uploads->propertydata;
            break;
        case _UPLOADS_GET_EXTERNAL:
            // minimum external import link must be: ftp://a.ws  <-- 10 characters total
            if (!xarVar::fetch('import', 'regexp:/^([a-z]*).\/\/(.{7,})/', $import, 'NULL', xarVar::NOT_REQUIRED)) {
                return;
            }
            $args['import'] = $import;
            break;
        case _UPLOADS_GET_LOCAL:
            if (!xarVar::fetch('fileList', 'list:regexp:/(?<!\.{2,2}\/)[\w\d]*/', $fileList)) {
                return;
            }
            if (!xarVar::fetch('file_all', 'checkbox', $file_all, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('addbutton', 'str:1', $addbutton, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('delbutton', 'str:1', $delbutton, '', xarVar::NOT_REQUIRED)) {
                return;
            }

            if (empty($addbutton) && empty($delbutton)) {
                $msg = xarML('Unsure how to proceed - missing button action!');
                throw new Exception($msg);
            } else {
                $args['bAction'] = (!empty($addbutton)) ? $addbutton : $delbutton;
            }

            $cwd = xarModUserVars::get('uploads', 'path.imports-cwd');
            foreach ($fileList as $file) {
                $args['fileList']["$cwd/$file"] = xarMod::apiFunc(
                    'uploads',
                    'user',
                    'file_get_metadata',
                    array('fileLocation' => "$cwd/$file")
                );
            }
            $args['getAll'] = $file_all;

            break;
        default:
        case _UPLOADS_GET_REFRESH_LOCAL:
            if (!xarVar::fetch('inode', 'regexp:/(?<!\.{2,2}\/)[\w\d]*/', $inode, '', xarVar::NOT_REQUIRED)) {
                return;
            }

            $cwd = xarMod::apiFunc('uploads', 'user', 'import_chdir', array('dirName' => isset($inode) ? $inode : null));

            $data['storeType']['DB_FULL']     = _UPLOADS_STORE_DB_FULL;
            $data['storeType']['FSDB']        = _UPLOADS_STORE_FSDB;
            $data['inodeType']['DIRECTORY']   = _INODE_TYPE_DIRECTORY;
            $data['inodeType']['FILE']        = _INODE_TYPE_FILE;
            $data['getAction']['LOCAL']       = _UPLOADS_GET_LOCAL;
            $data['getAction']['EXTERNAL']    = _UPLOADS_GET_EXTERNAL;
            $data['getAction']['UPLOAD']      = _UPLOADS_GET_UPLOAD;
            $data['getAction']['REFRESH']     = _UPLOADS_GET_REFRESH_LOCAL;
            $data['local_import_post_url']    = xarController::URL('uploads', 'admin', 'get_files');
            $data['external_import_post_url'] = xarController::URL('uploads', 'admin', 'get_files');
            $data['fileList'] = xarMod::apiFunc(
                'uploads',
                'user',
                'import_get_filelist',
                array('fileLocation' => $cwd, 'onlyNew' => true)
            );

            $data['curDir'] = str_replace(xarModVars::get('uploads', 'imports_directory'), '', $cwd);
            $data['noPrevDir'] = (xarModVars::get('uploads', 'imports_directory') == $cwd) ? true : false;
            // reset the CWD for the local import
            // then only display the: 'check for new imports' button
            $data['authid'] = xarSec::genAuthKey();
            $data['file_maxsize'] = $file_maxsize;
            return $data;
            break;
    }
    if (isset($storeType)) {
        $args['storeType'] = $storeType;
    }
    $list = xarMod::apiFunc('uploads', 'user', 'process_files', $args);
    if (is_array($list) && count($list)) {
        return xarTpl::module('uploads', 'admin', 'addfile-status', array('fileList' => $list), null);
    } else {
        xarController::redirect(xarController::URL('uploads', 'admin', 'get_files'));
        return;
    }

    return $data;
}
