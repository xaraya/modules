<?php

/**
 *  Retrieves an external file using the File scheme
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   array  uri     the array containing the broken down url information
 *  @returns array          FALSE on error, otherwise an array containing the fileInformation
 */
 
function uploads_userapi_import_external_file( $args ) 
{
         
    extract($args);
    
    if (!isset($uri) || !isset($uri['path'])) {
        return; // error
    }
    
    // create the URI 
    $fileURI = "$uri[scheme]://$uri[path]";
    
    $fileList = xarModAPIFunc('uploads', 'user', 'import_get_filelist', array('fileLocation' => $uri['path'], 'descend' => TRUE));
    
    $list = array();
    foreach ($fileList as $location => $fileInfo) {
        if ($fileInfo['inodeType'] == _INODE_TYPE_DIRECTORY) {
            $list += xarModAPIFunc('uploads', 'user', 'import_get_filelist',
                                    array('fileLocation' => $location, 'descend' => TRUE));
            unset($fileList[$location]);
        }
    }
    $fileList += $list;
    unset($list);
    
    
    return $fileList;
    
 }
 
 ?>
