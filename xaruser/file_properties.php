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
 *  Display a file properties window detailing information about the file
 *
 * @param int fileId
 * @param string fileName
 * @return array
 */
xarMod::apiLoad('uploads', 'user');

function uploads_user_file_properties($args)
{
    extract($args);

    if (!xarSecurity::check('ViewUploads')) {
        return;
    }
    if (!xarVar::fetch('fileId', 'int:1', $fileId)) {
        return;
    }
    if (!xarVar::fetch('fileName', 'str:1:64', $fileName, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!isset($fileId)) {
        $msg = xarML(
            'Missing paramater [#(1)] for GUI function [#(2)] in module [#(3)].',
            'fileId',
            'file_properties',
            'uploads'
        );
        throw new Exception($msg);
    }

    $fileInfo = xarMod::apiFunc('uploads', 'user', 'db_get_file', ['fileId' => $fileId]);
    if (empty($fileInfo) || !count($fileInfo)) {
        $data['fileInfo']   = [];
        $data['error']      = xarML('File not found!');
    } else {
        // the file should be the first indice in the array
        $fileInfo = end($fileInfo);

        $instance[0] = $fileInfo['fileTypeInfo']['typeId'];
        $instance[1] = $fileInfo['fileTypeInfo']['subtypeId'];
        $instance[2] = xarSession::getVar('uid');
        $instance[3] = $fileId;

        $instance = implode(':', $instance);
        if (xarSecurity::check('EditUploads', 0, 'File', $instance)) {
            $data['allowedit'] = 1;
            $data['hooks'] = xarModHooks::call(
                'item',
                'modify',
                $fileId,
                ['module'    => 'uploads',
                                                   'itemtype'  => 1, ]
            );
        } else {
            $data['allowedit'] = 0;
        }

        if (isset($fileName) && !empty($fileName)) {
            if ($data['allowedit']) {
                $args['fileId'] = $fileId;
                $args['fileName'] = trim($fileName);

                if (!xarMod::apiFunc('uploads', 'user', 'db_modify_file', $args)) {
                    $msg = xarML(
                        'Unable to change filename for file: #(1) with file Id #(2)',
                        $fileInfo['fileName'],
                        $fileInfo['fileId']
                    );
                    throw new Exception($msg);
                }
                xarController::redirect(xarController::URL('uploads', 'user', 'file_properties', ['fileId' => $fileId]));
                return;
            } else {
                xarErrorHandled();
                $msg = xarML('You do not have the necessary permissions for this object.');
                throw new Exception($msg);
            }
        }

        if ($fileInfo['fileStatus'] == _UPLOADS_STATUS_APPROVED || xarSecurity::check('ViewUploads', 1, 'File', $instance)) {


            // we don't want the theme to show up, so
            // get rid of everything in the buffer
            ob_end_clean();

            $storeType  = ['long' => '', 'short' => $fileInfo['storeType']];
            $storeType['long'] = 'Database File Entry';

            if (_UPLOADS_STORE_FILESYSTEM & $fileInfo['storeType']) {
                if (!empty($storeType['long'])) {
                    $storeType['long'] .= ' / ';
                }
                $storeType['long'] .= 'File System Store';
            } elseif (_UPLOADS_STORE_DB_DATA & $fileInfo['storeType']) {
                if (!empty($storeType['long'])) {
                    $storeType['long'] .= ' / ';
                }
                $storeType['long'] .= 'Database Store';
            }

            $fileInfo['storeType'] = $storeType;
            unset($storeType);

            $fileInfo['size'] = xarMod::apiFunc('uploads', 'user', 'normalize_filesize', ['fileSize' => $fileInfo['fileSize']]);

            if (mb_ereg('^image', $fileInfo['fileType'])) {
                // let the images module handle it
                if (xarMod::isAvailable('images')) {
                    $fileInfo['image'] = true;

                // try to get the image size
                } elseif (file_exists($fileInfo['fileLocation'])) {
                    $imageInfo = @getimagesize($fileInfo['fileLocation']);
                    if (is_array($imageInfo)) {
                        if ($imageInfo['0'] > 100 || $imageInfo[1] > 400) {
                            $oWidth  = $imageInfo[0];
                            $oHeight = $imageInfo[1];

                            $ratio = $oHeight / $oWidth;

                            // MAX WIDTH is 200 for this preview.
                            $newWidth  = 100;
                            $newHeight = round($newWidth * $ratio, 0);

                            $fileInfo['image']['height'] = $newHeight;
                            $fileInfo['image']['width']  = $newWidth;
                        } else {
                            $fileInfo['image']['height'] = $imageInfo[1];
                            $fileInfo['image']['width']  = $imageInfo[0];
                        }
                    }

                    // check if someone else already stored this information
                } elseif (!empty($fileInfo['extrainfo']) && !empty($fileInfo['extrainfo']['width'])) {
                    $fileInfo['image']['height'] = $fileInfo['extrainfo']['height'];
                    $fileInfo['image']['width']  = $fileInfo['extrainfo']['width'];
                }

                if (empty($fileInfo['image'])) {
                    $fileInfo['image']['height'] = '';
                    $fileInfo['image']['width']  = '';
                }
            }

            $fileInfo['numassoc'] = xarMod::apiFunc(
                'uploads',
                'user',
                'db_count_associations',
                ['fileId' => $fileId]
            );

            $data['fileInfo'] = $fileInfo;

            echo xarTpl::module('uploads', 'user', 'file_properties', $data, null);
            exit();
        } else {
            xarErrorHandled();
            $msg = xarML('You do not have the necessary permissions for this object.');
            throw new Exception($msg);
        }
    }

    return $data;
}
