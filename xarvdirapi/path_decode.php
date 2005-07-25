<?php

/**
 * Return the decoded Virtual Directory Path for the specified directory
 *
 * @author  Carl P. Corliss (ccorliss@schwabfoundation.org)
 * @param   string $path    path of the directory to decode
 * @returns integer
 * @return  the vdir_id of directory represented by the specified path
 */

function uploads_vdirapi_path_decode( $args )
{
    $path       = NULL;
    $is_mounted = FALSE;
    $pathInfo['dirId']  = 0;
    $pathInfo['fileId'] = 0;
    $pathInfo['is_mounted']   = FALSE;
    $pathInfo['is_cached']    = FALSE;
    $pathInfo['is_file']      = FALSE;
    $pathInfo['is_dir']       = FALSE;
    $mountInfo                = NULL;
    
    extract($args);

    if (!isset($path) || (!is_array($path) && !is_string($path))) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'path', 'vdir_path_decode', 'uploads');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;

    }

    // Do some sanity checking of the path
    if (isset($path) && !empty($path)) {
        if (is_array($path)) {
            $origPath = '/' . implode('/', $path);
        } else {
            $origPath = $path;
        }

        $path = xarModAPIFunc('uploads', 'vdir', 'split_path', array('path' => $path));
    } else {
        return FALSE;
    }

    $pathCache = xarVarGetCached('uploads', 'path.decoded');
    if (!is_array($pathCache)) {
        $pathCache = array();
    } elseif (isset($pathCache[md5($origPath)]) && !empty($pathCache[md5($origPath)])) {
        return $pathCache[md5($origPath)];
    }
        
    // Grab the complete directory list from the root on down
    $dirList = xarModAPIFunc('categories', 'user', 'getcat',
                              array('cid'           => xarModGetVar('uploads', 'folders.rootfs'),
                                    'getchildren'   => TRUE,
                                    'return_itself' => TRUE));

    $total           = count($path);
    $found_parent[1] = TRUE;
    $curIndex        = 1;
    
    // First thing we do is figure out if the given
    // path is a path that points to a directory
    foreach ($dirList as $entry) {
        if ($curIndex == ($entry['indentation']) &&     // If the indentation matches
            TRUE == $found_parent[$curIndex] &&         // and the parent was found for this
            $entry['name'] == $path[$curIndex]) {       // and the name matches
                if (xarModAPIFunc('uploads', 'mount', 'is_mountpoint', array('vdir_id' => $entry['cid']))) {
                    
                    // Get the stored information on the mount point
                    $mountInfo = xarModAPIFunc('uploads', 'mount', 'get', array('vdir_id' => $entry['cid']));

                    if (file_exists($mountInfo['path']) && is_dir($mountInfo['path'])) {
                        // Remove the current path component from
                        // the path list so we can correctly build the
                        // filesystem path to the file/directory
                        if (isset($path[$curIndex])) {
                            unset($path[$curIndex]);
                        }
                        $pathInfo['is_mounted'] = TRUE;
                        $dirId = $entry['cid'];
                        unset($path[$curIndex]);
                        break;
                    }

                    // If we've made it here we possibly have a malformed
                    // mount, so let's drop an error message so the user
                    // knows that something is terribly wrong here
                    $path = xarModAPIFunc('uploads', 'vdir', 'path_encode', array('vdir_id' => $entry['cid']));
                    $msg = xarML('Location pointed to by mountpoint: [#(1)] does not exist...', $path);
                    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_MOUNT_POINT_CORRUPT', new SystemException($msg));
                    return FALSE;
                }

                // If we're at the end of our path array, then
                // we have found what we were looking for
                if ($curIndex == $total) {
                    $dirId = $entry['cid'];
                    unset($path[$curIndex]);
                    break;
                } else {
                    $lastId = $entry['cid'];
                    $curIndex++;
                    $found_parent[$curIndex] = TRUE;
                }
        }

        // Remove the path component that we just checked
        // This is useful, in particular, for the case where
        // we encounter a mount point and need to return
        // the file system path to the actual file/directory
        if (isset($path[$curIndex - 1])) {
            unset($path[$curIndex - 1]);
        }
    }

    if (isset($lastId) && !isset($dirId)) {
        $dirId = $lastId;
    }

    $pathInfo['dirId'] = $dirId;

    // If we still have path components, we either have a subdir of a mounted
    // directory, or we have a path to a file
    if (count($path)) {
        // If we're not dealing with a mounted path,
        // then we should have a path to a stored file
        if (FALSE == $pathInfo['is_mounted']) {
            // If not mounted, then attempt to find the file
            // in the directory specified by dirId 
            $assocList = xarModAPIFunc('uploads', 'user', 'db_get_associations',
                                        array('modid'  => xarModGetIdFromName('categories'),
                                              'itemtype' => 0,
                                              'itemid' => $dirId));
            $file = @end(xarModAPIFunc('uploads', 'user', 'db_get_file_entry',
                                        array('fileId' => array_keys($assocList),
                                              'fileName' => end($path))));

            if (!isset($file) || empty($file)) {
                $msg = xarML('Unable to find file or directory [#(1)]', $origPath);
                xarErrorSet(XAR_USER_EXCEPTION, 'ERR_NO_EXIST', new SystemException($msg));
                return FALSE;
            } else {
                $pathInfo['fileId']    = $file['id'];
                $pathInfo['is_file']   = TRUE;
                $pathInfo['is_cached'] = TRUE;
            }                

        } else {
            // If it's a mounted path/file, we handle things a bit differently
            if (isset($mountInfo['path']) && !empty($mountInfo['path'])) {
                $filePath       = str_replace('//', '/', $mountInfo['path'] . '/' . implode('/', $path));
                $fileLocation   = 'mount://' . str_replace('//', '/', "$dirId/" . implode('/', $path));
                
                if (file_exists($filePath)) {
                    if (is_file($filePath)) {
                        $file = @end(xarModAPIFunc('uploads', 'user', 'db_get_file_entry',
                                     array('fileLocation' => $fileLocation)));
                                                    
                        if (isset($file['id']) && !empty($file['id'])) {
                            $pathInfo['fileId']    = $file['id'];
                            $pathInfo['is_dir']    = FALSE;
                            $pathInfo['is_file']   = TRUE;
                            $pathInfo['is_cached'] = TRUE;
                        } else {
                            $addFileArgs['name']     = end($path);
                            $addFileArgs['destination'] = $fileLocation;

                            if (($fileId = xarModAPIFunc('uploads', 'user', 'db_add_file', $addFileArgs))) {
                                // Cool - it was added, so now add the
                                // association with the mount point directory
                                xarModAPIFunc('uploads', 'user', 'db_add_association',
                                               array('modid'    => xarModGetIDFromName('categories'),
                                                     'itemtype' => 0,
                                                     'itemid'   => $dirId,
                                                     'fileid'   => $fileId));
                                 $pathInfo['fileId']    = $fileId;
                                 $pathInfo['is_file']   = TRUE;
                                 $pathInfo['is_cached'] = TRUE;
                            } else {
                                // Doh - didn't add it. let's at least acknowledge
                                // that this is a file and not a directory path
                                $pathInfo['is_file']   = TRUE;
                            }
                        }
                    } elseif (is_dir($filePath)) {
                         $pathInfo['is_dir']    = TRUE;
                         $pathInfo['extendedPath'] = implode('/', $path);
                    }
                } 
            } 
        }

        if (isset($file) && !empty($file)) {
            $pathInfo['fileId'] = $file['id'];
        }
    } 
    
    if ($pathInfo['dirId'] && !$pathInfo['fileId']) {
        $pathInfo['is_dir'] = TRUE;
    }
    $pathCache[md5($origPath)] = $pathInfo;
    xarVarSetCached('uploads', 'path.decoded', $pathCache);
    return $pathInfo;
}

?>
