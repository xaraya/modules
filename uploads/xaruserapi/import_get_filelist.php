<?php

/** 
 *  Rename a file. (alias for file_move)
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   <type>
 *  @returns <type> 
 */

function uploads_userapi_import_get_filelist( $args ) {
    
    
    extract($args);
    
    if (!isset($descend)) {
        $descend = FALSE;
    }
    
    $fileList = array();    
    
    if (!isset($fileLocation)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                     'fileLocation', 'import_get_filelist', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
    if (!file_exists($fileLocation)) {
        $msg = xarML('Unable to acquire list of files to import - Location does not exist!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_EXIST', new SystemException($msg));
        return;
    }
    
    if (is_file($fileLocation)) {
        $type = _INODE_TYPE_FILE;
    } elseif(is_dir($fileLocation)) {
        $type = _INODE_TYPE_DIRECTORY;
    } else {
        $type = -1;
    }
    switch ($type) {
        case _INODE_TYPE_FILE:
            $fileName = $fileLocation;
            $fileList[$fileName] = xarModAPIFunc('uploads', 'user', 'file_get_metadata', 
                                                  array('fileLocation' => $fileLocation));
            break;
        case _INODE_TYPE_DIRECTORY:
            if ($fp = opendir($fileLocation)) {

                while (FALSE !== ($inode = readdir($fp))) { 
                    if (is_link($fileLocation. '/' . $inode)) {
                        continue;
                    }

                    if (is_dir($fileLocation. '/' . $inode) && !eregi('^([.]{1,2})$', $inode)) {
                        $dirName = "$fileLocation/$inode";
                        if ($descend) {
                            $files = xarModAPIFunc('uploads', 'user', 'import_get_filelist', 
                                                    array('fileLocation' => $dirName,
                                                          'descend' => TRUE));
                            $fileList += $files;
                        } else {
                            $files = xarModAPIFunc('uploads', 'user', 'file_get_metadata', array('fileLocation' => $dirName));

                            // Now we add the fileList from the directory
                            // to the directories inode in the direoctory list
                            $fileList["$files[inodeType]:$inode"] = $files;
                        }
                    } 
                    if (is_file($fileLocation. '/' . $inode)) {
                        $fileName = $fileLocation . '/' . $inode;
                        $fileList[$fileName] = xarModAPIFunc('uploads', 'user', 'file_get_metadata', array('fileLocation' => $fileName));
                    }
                }
            }
            closedir($fp);
            break;
        default:
            break;
    }
    
    if (is_array($fileList)) {
        ksort($fileList);
    }

    return $fileList;
}

?>
