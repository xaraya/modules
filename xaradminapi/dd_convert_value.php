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

function uploads_adminapi_dd_convert_value($args)
{

    extract($args);

    if (!isset($value)) {
        return NULL;
    }

    if (!isset($basedir)) {
        // try something here in hopes that it works.
        $basedir = 'var/uploads/';
    }

    // if conversion isn't needed, then don't do it
    if (!xarModAPIFunc('uploads', 'admin', 'dd_value_needs_conversion', $value)) {
        return $value;
    }

    if (!isset($basePath)) {

        if(xarServerGetVar('SCRIPT_FILENAME')) {
            $base_directory = dirname(realpath(xarServerGetVar('SCRIPT_FILENAME')));
        } else {
            $base_directory = './';
        }

        $basePath = $base_directory;
    }

    if (file_exists($basedir . $value) && !is_file($basedir . $value)) {
        xarModAPILoad('uploads', 'user');

        $args['import'] = 'file://' . $basePath . '/' . $basedir . $value;
        $args['action'] = _UPLOADS_GET_EXTERNAL;
        $list = xarModAPIFunc('uploads','user','process_files', $args);
        $storeList = array();
        foreach ($list as $file => $fileInfo) {
            if (!isset($fileInfo['errors'])) {
                $storeList[] = $fileInfo['fileId'];
            } else {
                $msg = xarML('Error Found: #(1)', $fileInfo['errors'][0]['errorMesg']);
                xarErrorSet(XAR_USER_EXCEPTION, 'UNKNOWN_ERROR', new DefaultUserException($msg));

                return $value;
            }
        }

        if (is_array($storeList) && count($storeList)) {
            // We prepend a semicolon onto the list of fileId's so that
            // we can tell, in the future, that this is a list of fileIds
            // and not just a filename

            return $value = ';' . implode(';', $storeList);
        } else {
            // if we've managed to get here, then just return the original value
            return $value;
        }
    } else {
        // do nothing for now - until i find a way to ensure that
        // all files can be migrated...
        // if we've managed to get here, then just return the original value
        return $value;
    }

}

?>