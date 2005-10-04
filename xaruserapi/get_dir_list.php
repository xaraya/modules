<?php


function filemanager_userapi_get_dir_list( $args )
{


    extract($args);

    if (!isset($path) || empty($path) || !is_string($path)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'path', 'get_dir_,list', 'filemanager');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($linkInfo['func'])) {
        $linkInfo['func'] = 'file_browser';
    }

    if (!isset($linkInfo['args'])) {
        $linkInfo['args'] = array();
    }

    $mountpoints = @unserialize(xarModGetVar('filemanager', 'mount.list'));
    if (!is_array($mountpoints)) {
        $mountpoints = array();
    }

    $vdirInfo = xarModAPIFunc('filemanager', 'vdir', 'path_decode', array('path' => $path));

    if (FALSE !== $vdirInfo) {

        $dirList = xarModAPIFunc('filemanager', 'vdir', 'get_dir_list',
                                  array('vdir_id'  => $vdirInfo['dirId'],
                                        'path'     => $path,
                                        'linkInfo' => $linkInfo));

        if (in_array($vdirInfo['dirId'], array_keys($mountpoints))) {
            $pathRoot = $mountpoints[$vdirInfo['dirId']]['path'];
            $pathDest = $path;
            $tree     =& $dirList;

            // Find the directory that we are going to append
            // children to and grab a reference to it
            $pathArray = xarModAPIFunc('filemanager', 'vdir', 'split_path', array('path' => $path));
            $node = 1;

            while (true) {
                if (isset($pathArray[$node])) {
                    $pathValue = $pathArray[$node];
                    if (isset($tree[$pathValue])) {
                        $tree =& $tree[$pathArray[$node]]['children'];
                        unset($pathArray[$node]);
                        $node++;
                        continue;
                    }
                }
                break;
            }

            $pathDest = implode('/', $pathArray);

            if (!strlen($pathDest) || $pathDest{strlen($pathDest) - 1} != '/') {
                $pathDest .=  '/';
            }

            // Now grab the directory tree from the filesystem
            $fs_dirlist = xarModAPIfunc('filemanager', 'fs', 'get_dir_list',
                                         array('pathRoot' => $pathRoot,
                                               'pathDest' => $pathDest,
                                               'linkInfo' => $linkInfo));

            if (!isset($fs_dirlist)) {
                // If it's VOID, then we probably
                // recvd and error, so return it
                return;
            }
            // And add it to the previously grabbed reference into our tree
            $tree = $fs_dirlist;

        }
        return $dirList;
    } else {
        return FALSE;
    }
}

?>
