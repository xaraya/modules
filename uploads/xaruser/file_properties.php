<?php

/**
 *  Display a file properties window detailing information about the file
 */
xarModAPILoad('uploads','user');

function uploads_user_file_properties( $args ) {

    if (!xarVarFetch('fileId', 'int:1', $fileId)) return;
    
    if (!isset($fileId)) {
        $msg = xarML('Missing paramater [#(1)] for GUI function [#(2)] in module [#(3)].',
                     'fileId', 'file_properties', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
    $fileInfo = xarModAPIFunc('uploads','user','db_get_file',
                               array('fileId' => $fileId));
    if (empty($fileInfo)) {
        $data['fileInfo']   = array();
        $dtaa['error']      = xarML('File not found!');
    } else {
        // we don't want the theme to show up, so 
        // get rid of everything in the buffer
        ob_end_clean();
        
        $fileInfo   = $fileInfo[0];
        $storeType  = $fileInfo['storeType'];
        $fileInfo['storeType'] = array('long' => '', 'short' => $storeType);
        $storeType =& $fileInfo['storeType'];
        
        if (_UPLOADS_STORE_DB_ENTRY & $fileInfo['storeType']) {
            $storeType['long'] = 'Database File Entry';
        }
        
        if (_UPLOADS_STORE_FILESYSTEM & $fileInfo['storeType']) {
            if (!empty($storeType['long'])) {
                $storeType['long'] .= ' / ';
            }
            $fileInfo['storeType']['long'] .= 'File System Store';
        }
        
        if (_UPLOADS_STORE_DB_DATA & $fileInfo['storeType']) {
            if (!empty($storeType['long'])) {
                $storeType['long'] .= ' / ';
            }
            $fileInfo['storeType']['long'] .= 'Database Store';
        }
        unset($storeType);
        
        $fileInfo['size']['long']  = number_format($fileInfo['fileSize']);
        
        $size = $fileInfo['fileSize'];
        $range = array('', 'Kb', 'Mb', 'Gb');
        for ($i = 0; $size >= 1024 && $i < count($range); $i++) {
            $size /= 1024;
        }
        $short = round($size, 2).' '.$range[$i];
        $fileInfo['size']['short']  = $short;
        
        $data['fileInfo'] = $fileInfo;
        echo xarTplModule('uploads','user','file_properties', $data, NULL);
        exit();
    }
         
    return $data;

}
?>