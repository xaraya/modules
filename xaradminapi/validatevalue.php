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
 * validate input values for uploads module (used in DD properties)
 *
 * @param  $args ['id'] string id of the upload field(s)
 * @param  $args ['value'] string the current value(s)
 * @param  $args ['format'] string format specifying 'fileupload', 'textupload' or 'upload' (future ?)
 * @param  $args ['multiple'] boolean allow multiple uploads or not
 * @param  $args ['maxsize'] integer maximum size for upload files
 * @param  $args ['methods'] array allowed methods 'trusted', 'external', 'stored' and/or 'upload'
 * @param  $args ['override'] array optional override values for import/upload path/obfuscate (cfr. process_files)
 * @param  $args ['moduleid'] integer optional module id for keeping file associations
 * @param  $args ['itemtype'] integer optional item type for keeping file associations
 * @param  $args ['itemid'] integer optional item id for keeping file associations
 * @return array
 * @return array of (result, value) with result true, false or NULL (= error)
 */
function uploads_adminapi_validatevalue($args)
{
    extract($args);
    if (empty($id)) {
        $id = null;
    }
    if (empty($value)) {
        $value = null;
    }
    if (empty($format)) {
        $format = 'fileupload';
    }
    if (empty($multiple)) {
        $multiple = false;
    } else {
        $multiple = true;
    }
    if (empty($maxsize)) {
        $maxsize = xarModVars::get('uploads', 'file.maxsize');
    }
    if (empty($methods)) {
        $methods = null;
    }
    if (empty($itemtype)) {
        $itemtype = 0;
    }

    // Check to see if an old value is present. Old values just file names
    // and do not start with a semicolon (our delimiter)
    if (xarMod::apiFunc('uploads', 'admin', 'dd_value_needs_conversion', $value)) {
        $newValue = xarMod::apiFunc('uploads', 'admin', 'dd_convert_value', ['value' =>$value]);

        // if we were unable to convert the value, then go ahead and and return
        // an empty string instead of processing the value and bombing out
        if ($newValue == $value) {
            $value = null;
            unset($newValue);
        } else {
            $value = $newValue;
            unset($newValue);
        }
    }

    xarMod::apiLoad('uploads', 'user');

    if (isset($methods) && count($methods) > 0) {
        $typeCheck = 'enum:0:' . _UPLOADS_GET_STORED;
        $typeCheck .= (isset($methods['external']) && $methods['external']) ? ':' . _UPLOADS_GET_EXTERNAL : '';
        $typeCheck .= (isset($methods['trusted']) && $methods['trusted']) ? ':' . _UPLOADS_GET_LOCAL : '';
        $typeCheck .= (isset($methods['upload']) && $methods['upload']) ? ':' . _UPLOADS_GET_UPLOAD : '';
        $typeCheck .= ':-2'; // clear value
    } else {
        $typeCheck = 'enum:0:' . _UPLOADS_GET_STORED;
        $typeCheck .= (xarModVars::get('uploads', 'dd.fileupload.external') == true) ? ':' . _UPLOADS_GET_EXTERNAL : '';
        $typeCheck .= (xarModVars::get('uploads', 'dd.fileupload.trusted') == true) ? ':' . _UPLOADS_GET_LOCAL : '';
        $typeCheck .= (xarModVars::get('uploads', 'dd.fileupload.upload') == true) ? ':' . _UPLOADS_GET_UPLOAD : '';
        $typeCheck .= ':-2'; // clear value
    }

    xarVar::fetch($id . '_attach_type', $typeCheck, $action, -3, xarVar::NOT_REQUIRED);

    if (!isset($action)) {
        $action = -3;
    }

    $args['action']    = $action;
    switch ($action) {
        case _UPLOADS_GET_UPLOAD:

            $file_maxsize = xarModVars::get('uploads', 'file.maxsize');
            $file_maxsize = $file_maxsize > 0 ? $file_maxsize : $maxsize;

            if (!xarVar::fetch('MAX_FILE_SIZE', "int::$file_maxsize", $maxsize)) {
                return;
            }

            if (!xarVar::validate('array:1:', $_FILES[$id . '_attach_upload'])) {
                return;
            }

            $upload         =& $_FILES[$id . '_attach_upload'];
            $args['upload'] =& $_FILES[$id . '_attach_upload'];
            break;
        case _UPLOADS_GET_EXTERNAL:
            // minimum external import link must be: ftp://a.ws  <-- 10 characters total

            if (!xarVar::fetch($id . '_attach_external', 'regexp:/^([a-z]*).\/\/(.{7,})/', $import, 0, xarVar::NOT_REQUIRED)) {
                return;
            }

            if (empty($import)) {
                // synchronize file associations with empty list
                if (!empty($moduleid) && !empty($itemid)) {
                    uploads_sync_associations($moduleid, $itemtype, $itemid);
                }
                return [true,null];
            }

            $args['import'] = $import;
            break;
        case _UPLOADS_GET_LOCAL:

            if (!xarVar::fetch($id . '_attach_trusted', 'list:regexp:/(?<!\.{2,2}\/)[\w\d]*/', $fileList)) {
                return;
            }

        // CHECKME: use 'imports' name like in db_get_file() ?
            // replace /trusted coming from showinput() again
            $importDir = sys::root() . "/" . xarModVars::get('uploads', 'imports_directory');
            foreach ($fileList as $file) {
                $file = str_replace('/trusted', $importDir, $file);
                $args['fileList']["$file"] = xarMod::apiFunc(
                    'uploads',
                    'user',
                    'file_get_metadata',
                    ['fileLocation' => "$file"]
                );
                if (isset($args['fileList']["$file"]['fileSize']['long'])) {
                    $args['fileList']["$file"]['fileSize'] = $args['fileList']["$file"]['fileSize']['long'];
                }
            }
            break;
        case _UPLOADS_GET_STORED:

            if (!xarVar::fetch($id . '_attach_stored', 'list:int:1:', $fileList, 0, xarVar::NOT_REQUIRED)) {
                return;
            }


            // If we've made it this far, then fileList was empty to start,
            // so don't complain about it being empty now
            if (empty($fileList) || !is_array($fileList)) {
                // synchronize file associations with empty list
                if (!empty($moduleid) && !empty($itemid)) {
                    uploads_sync_associations($moduleid, $itemtype, $itemid);
                }
                return [true,null];
            }

            // We prepend a semicolon onto the list of fileId's so that
            // we can tell, in the future, that this is a list of fileIds
            // and not just a filename
            $value = ';' . implode(';', $fileList);

            // synchronize file associations with file list
            if (!empty($moduleid) && !empty($itemid)) {
                uploads_sync_associations($moduleid, $itemtype, $itemid, $fileList);
            }

            return [true,$value];
            break;
        case '-1':
            return [true,$value];
            break;
        case '-2':
            // clear stored value
            return [true, null];
            break;
        default:
            if (isset($value)) {
                if (strlen($value) && $value[0] == ';') {
                    return [true,$value];
                } else {
                    return [false,null];
                }
            } else {
                // If we have managed to get here then we have a NULL value
                // and $action was most likely either null or something unexpected
                // So let's keep things that way :-)
                return [true,null];
            }
            break;
    }

    if (!empty($action)) {
        if (isset($storeType)) {
            $args['storeType'] = $storeType;
        }

        $list = xarMod::apiFunc('uploads', 'user', 'process_files', $args);
        $storeList = [];
        foreach ($list as $file => $fileInfo) {
            if (!isset($fileInfo['errors'])) {
                $storeList[] = $fileInfo['fileId'];
            } else {
                $msg = xarML('Error Found: #(1)', $fileInfo['errors'][0]['errorMesg']);
                throw new Exception($msg);
            }
        }
        if (is_array($storeList) && count($storeList)) {
            // We prepend a semicolon onto the list of fileId's so that
            // we can tell, in the future, that this is a list of fileIds
            // and not just a filename
            $value = ';' . implode(';', $storeList);

            // synchronize file associations with store list
            if (!empty($moduleid) && !empty($itemid)) {
                uploads_sync_associations($moduleid, $itemtype, $itemid, $storeList);
            }
        } else {
            return [false,null];
        }
    } else {
        return [false,null];
    }

    return [true,$value];
}

/**
 * Utility function to synchronise file associations on validation
 * (for create/update of DD extra fields + update of DD objects and articles)
 */
function uploads_sync_associations($moduleid = 0, $itemtype = 0, $itemid = 0, $filelist = [])
{
    // see if we have anything to work with
    if (empty($moduleid) || empty($itemid)) {
        return;
    }

    // (try to) check if we're previewing or not
    xarVar::fetch('preview', 'isset', $preview, false, xarVar::NOT_REQUIRED);
    if (!empty($preview)) {
        return;
    }

    // get the current file associations for this module items
    $assoc = xarMod::apiFunc(
        'uploads',
        'user',
        'db_get_associations',
        ['modid'    => $moduleid,
                                 'itemtype' => $itemtype,
                                 'itemid'   => $itemid, ]
    );

    // see what we need to add or delete
    if (!empty($assoc) && count($assoc) > 0) {
        $add = array_diff($filelist, array_keys($assoc));
        $del = array_diff(array_keys($assoc), $filelist);
    } else {
        $add = $filelist;
        $del = [];
    }

    foreach ($add as $id) {
        if (empty($id)) {
            continue;
        }
        xarMod::apiFunc(
            'uploads',
            'user',
            'db_add_association',
            ['fileId'   => $id,
                            'modid'    => $moduleid,
                            'itemtype' => $itemtype,
                            'itemid'   => $itemid, ]
        );
    }
    foreach ($del as $id) {
        if (empty($id)) {
            continue;
        }
        xarMod::apiFunc(
            'uploads',
            'user',
            'db_delete_association',
            ['fileId'   => $id,
                            'modid'    => $moduleid,
                            'itemtype' => $itemtype,
                            'itemid'   => $itemid, ]
        );
    }
}
