<?php

/**
 *  Display a file properties window detailing information about the file
 */
xarModAPILoad('uploads','user');

function uploads_user_file_properties( $args ) 
{
    
    extract($args);

    if (!xarSecurityCheck('ViewUploads')) return;
    if (!xarVarFetch('fileId',   'int:1', $fileId)) return;
    if (!xarVarFetch('fileName', 'str:1:64', $fileName, '', XARVAR_NOT_REQUIRED)) return;
    
    if (!isset($fileId)) {
        $msg = xarML('Missing paramater [#(1)] for GUI function [#(2)] in module [#(3)].',
                     'fileId', 'file_properties', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
    $fileInfo = xarModAPIFunc('uploads','user','db_get_file', array('fileId' => $fileId));
    if (empty($fileInfo) || !count($fileInfo)) {
        $data['fileInfo']   = array();
        $dtaa['error']      = xarML('File not found!');
    } else {
        // the file should be the first indice in the array
        $fileInfo = end($fileInfo);

        $instance[0] = $fileInfo['fileTypeInfo']['typeId'];
        $instance[1] = $fileInfo['fileTypeInfo']['subtypeId'];
        $instance[2] = xarSessionGetVar('uid');
        $instance[3] = $fileId;

        $instance = implode(':', $instance);

        if (isset($fileName) && !empty($fileName)) {
            
            if (xarSecurityCheck('EditUploads', 1, 'File', $instance)) {
                $args['fileId'] = $fileId;
                $args['fileName'] = trim($fileName);
                
                if (!xarModAPIFunc('uploads', 'user', 'db_modify_file', $args)) {
                    $msg = xarML('Unable to change filename for file: #(1) with file Id #(2)', 
                                  $fileInfo['fileName'], $fileInfo['fileId']);
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN_ERROR', new SystemException($msg));
                    return;
                }
                xarResponseRedirect(xarModURL('uploads', 'user', 'file_properties', array('fileId' => $fileId)));
                return;
            } else {
                // No access - so return the exception
                return;
            }
        }
        
        if (xarSecurityCheck('ViewUploads', 1, 'File', $instance)) {


            // we don't want the theme to show up, so 
            // get rid of everything in the buffer
            ob_end_clean();

            $storeType  = array('long' => '', 'short' => $fileInfo['storeType']);
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

            $fileInfo['size'] = xarModAPIFunc('uploads', 'user', 'normalize_filesize', array('fileSize' => $fileInfo['fileSize']));

            if (ereg('^image', $fileInfo['fileType'])) {
                if (xarModIsAvailable('images')) {
                    $fileInfo['image'] = TRUE;
                } else {
                    $imageInfo = getimagesize($fileInfo['fileLocation']);
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
                }
            }


            $data['fileInfo'] = $fileInfo;

            echo xarTplModule('uploads','user','file_properties', $data, NULL);
            exit();
        } else {
            $data['fileInfo']   = array();
            $dtaa['error']      = xarML('File not found!');
            return FALSE;
        }
    }
    
    return $data;

}
?>