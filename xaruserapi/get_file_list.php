<?php


function filemanager_userapi_get_file_list( $args )
{

    $path       = NULL;
    $files      = array();
    $sortdir    = _FILEMANAGER_VDIR_SORT_ASC;
    $sortby     = _FILEMANAGER_VDIR_SORTBY_NAME;
    $fileExList = array();
    $linkInfo   = array('func' => 'download',
                        'args' => array());
    $list       = array();
    
    extract($args);

    if (!isset($path) || empty($path) || !is_string($path)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'path', 'get_dir_,list', 'filemanager');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    $vdirInfo = xarModAPIFunc('filemanager', 'vdir', 'path_decode', array('path' => $path));

    $mountpoints = @unserialize(xarModGetVar('filemanager', 'mount.list'));
    if (!is_array($mountpoints)) {
        $mountpoints = array();
    }

    if (FALSE !== $vdirInfo) {

        // If we're dealing with a mounted directory
        // then grab the file list using fs_get_dir_contents()
        // otherwise, we use vdir_get_dir_contents()
        if (in_array($vdirInfo['dirId'], array_keys($mountpoints))) {
           $files = xarModAPIFunc('filemanager', 'fs', 'get_dir_contents', 
                                   array('path'     => $path, 
                                         'vdir_id'  => $vdirInfo['dirId'],
                                         'linkInfo' => $linkInfo));
        } else {
            $files = xarModAPIFunc('filemanager', 'vdir', 'get_dir_contents', 
                                    array('vdir_id'  => $vdirInfo['dirId'], 
                                          'linkInfo' => $linkInfo));
        }
    } else {
        return array();
    }

    foreach ($files as $entry => $data) {

        switch($sortby) {
            case _FILEMANAGER_VDIR_SORTBY_DATE:
                $hash = "f.{$data['time']}.{$data['name']}";
                break;
            case _FILEMANAGER_VDIR_SORTBY_TYPE:
                $hash = "f.{$data['type']}.{$data['name']}";
                break;
            case _FILEMANAGER_VDIR_SORTBY_SIZE:
                $size = (string) $data['sizeval'];
                $hash = 'f.' . str_pad((string) $size, 20, '0', STR_PAD_LEFT) . '.' . $data['name'];
                break;
            case _FILEMANAGER_VDIR_SORTBY_OWNER:
                $hash = "f.{$data['owner']}.{$data['name']}";
                break;
            case _FILEMANAGER_VDIR_SORTBY_NAME:
            default:
                $hash = "f.{$data['name']}.{$data['sizeval']}.{$data['time']}";
                break;
        }

        $list[$hash] = $data;
        unset($files[$entry]);
    }

    switch($sortdir) {
        case _FILEMANAGER_VDIR_SORT_DESC:
            uksort($list, 'strnatcmp');
            $list = array_reverse($list);
            break;
        case _FILEMANAGER_VDIR_SORT_ASC:
        default:
            uksort($list, 'strnatcmp');
            break;
    }

    return $list;

}

?>
