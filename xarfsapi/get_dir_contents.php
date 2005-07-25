<?php

function uploads_fsapi_get_dir_contents( $args )
{
    $excludeFiles  = array();
    $path          = NULL;
    $vdir_id       = NULL;      // Virtual directory id 

    extract ($args);
    
    if (!isset($linkInfo['func']) || empty($linkInfo['func'])) {
        $linkInfo['func'] = 'download';
    }
    
    if (!isset($linkInfo['args']) || empty($linkInfo['args'])) {
        $linkInfo['args'] = array();
    }

    if (!isset($path) || !isset($vdir_id)) {
        return array();
    }

    if (is_array($path)) {
        $path = implode('/', $path);
    } else {
        if ($path{0} != '/') {
            $path = '/' . $path;
        }
    }

    $vdir_path = xarModAPIFunc('uploads', 'vdir', 'path_encode', array('vdir_id' => $vdir_id));
    $pathDest = str_replace($vdir_path, '', $path);
    $mountpoints = @unserialize(xarModGetVar('uploads', 'mount.list'));

    if (is_array($mountpoints) && in_array($vdir_id, array_keys($mountpoints))) {

        $path = $mountpoints[$vdir_id]['path'] . '/' . $pathDest;
        
        if (!isset($file_filter)) {
            $file_filter = $mountpoints[$vdir_id]['filter.file'];
        }

        if (!isset($dir_filter)) {
            $dir_filter = $mountpoints[$vdir_id]['filter.dir'];
        }
    } else {
        return array();
    }



    // if we have an exclude string, then exclude :-)
    if (!empty($dir_filter)) {
        if (eregi($dir_filter, $path)) {
            return array();
        }
    }
    
    if (!is_readable($path)) {
        $msg = xarML('Xaraya does not have access to read this directory...');
        xarErrorSet(XAR_USER_EXCEPTION, 'UPLOADS_FOLDER_UNREADABLE', new SystemException($msg));
        return NULL;
    }

    if (is_file($path)) {
        $path = dirname($path);
    }
    
    $list = array();

    if (is_dir($path) && ($dirHandle = opendir($path))) {
        while (($file = readdir($dirHandle)) !== false) {
            
            $fullPath = ($path{strlen($path) - 1} != '/') ? $path . '/' . $file : $path . $file;
            
            if (!is_file($fullPath) && !is_link($fullPath) || !is_readable($fullPath) || in_array($file, $excludeFiles)) {
                continue;
            } else {

                // if we have an exclude string, then exclude :-)
                if (!empty($file_filter)) {
                    if (eregi($file_filter, $fullPath)) {
                        continue;
                    }
                }

                if (function_exists('readlink')) {
                    $linkpath = $fullPath;
                    if (is_link($linkpath)) {

                        do {
                            $linkpath = readlink($linkpath);
                        } while (is_link($linkpath));

                        if (!is_file($linkpath)) {
                            continue;
                        }
                    }
                }

                $filesize = xarModAPIfunc('uploads', 'user', 'normalize_filesize',
                                           array('fileSize' => filesize($fullPath)));
                $mtime = @filemtime($fullPath);
                $ctime = @filectime($fullPath);

                $linkfunc =& $linkInfo['func'];
                $linkargs =& $linkInfo['args'];
                $linkargs['vpath'] = str_replace('//', '/', $vdir_path . '/' . $pathDest . '/' . $file);

                $fileInfo = @end(xarModAPIFunc('uploads', 'user', 'db_get_file_entry', 
                                            array('fileLocation' => 'mount://' . $vdir_id . $pathDest)));
                                            
                $list[$file]['name']       = $file;

                if (isset($fileInfo['id']) && !empty($fileInfo['id'])) {
                    $list[$file]['id'] = $fileInfo['id'];
                } else {
                    $list[$file]['id'] = 0;
                }
                $list[$file]['link']       = '';
                
                $list[$file]['location']   = 'mount://' . str_replace('//', '/', $vdir_id . '/' . $pathDest . '/' . $file);
                $list[$file]['type']       = xarModAPIfunc('mime', 'user', 'analyze_file', array('fileName' => $fullPath));
                $list[$file]['type-image'] = xarModAPIFunc('mime', 'user', 'get_mime_image', array('mimeType' => $list[$file]['type']));
                $list[$file]['comment']    = '';
                $list[$file]['owner']      = xarML('Administrator');
                $list[$file]['size']       = $filesize['short'];
                $list[$file]['sizeval']    = @filesize($fullPath);
                $list[$file]['time']       = $mtime ? $mtime : ($ctime ? $ctime : 0);
                $list[$file]['link'] = xarModURL('uploads', 'user', $linkfunc, $linkargs);

            }
        }
    }

    return $list;
}
?>