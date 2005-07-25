<?php

/**
 * lists the contents of the specified directory returning the result as an
 * array of files and folders -> their metadata, in the order specified by sortby/direction
 *
 * @param   integer $vdir_id     ID of the directory whose contents we want to list
 * @param   integer $sortby      attribute to sort by: TYPE, NAME, SIZE, OWNER, CREATION_DATE
 * @param   integer $direction   ascending or descending
 * @returns array
 * @return  array of files and directories in the order specified by sortby/direction, or FALSE on error
 */

function uploads_vdirapi_list( $args )
{

    extract($args);

    if (!isset($vdir_id) || empty($vdir_id)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'vdir_id', 'vdir_list', 'uploads');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($sortby)) {
        // Note: directories always come before
        // files in a directory listing
        $sortby = _UPLOADS_VDIR_SORTBY_NAME;
    }

    if (!isset($direction)) {
        $direction = _UPLOADS_VDIR_SORT_ASC;
    }

    /**
      Grab all the directories in the specified directory (just the current level)
      grab all the files associated with this directory

      sort them in the order specified
      return the result
     */

    $directories = xarModAPIFunc('categories', 'user', 'getchildren',
                                   array('cid' => $vdir_id,
                                         'return_itself' => TRUE,
                                         'get_parents' => TRUE));

    if (empty($directories) || !is_array($directories)) {
        $directories = array();
    } else {
        $directories[$vdir_id]['name'] = '||current-dir||';
        $directories[$vdir_id]['description'] = xarML('Current Folder');

        $parent = $directories[$vdir_id]['parent'];
        $directories[$parent]['cid'] = $parent;
        $directories[$parent]['name'] = '||parent-dir||';
        $directories[$parent]['description'] = xarML('Parent Folder');
    }

    $files = xarModAPIFunc('uploads', 'user', 'db_get_associations',
                            array('modid'  => xarModGetIdFromName('categories'),
                                  'itemid' => $vdir_id,
                                  'itemtype' => 0,
                                  'indexby' => 'md5'));

    if (empty($files) || !is_array($files)) {
        $files = array();
    }

    $files = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => array_keys($files)));

    foreach ($directories as $entry => $data) {

        // TODO: modify so that by size works for
        //       directories, as well as by owner
        $sizeValue = xarModAPIFunc('uploads', 'vdir', 'size', array('vdir_id' => $data['cid']));
        $sizeArray = xarModAPIFunc('uploads', 'user', 'normalize_filesize', $sizeValue);

        // handle current and parent directory a little differently
        // they shouldn't be shown in the list and so shouldn't be
        // processed in the same manner

        switch($sortby) {
            case _UPLOADS_VDIR_SORTBY_CREATION:
                $hash = "d.$data[cid]";
                break;
            case _UPLOADS_VDIR_SORTBY_SIZE:
                $size = xarModAPIFunc('uploads', 'vdir', 'size', array('vdir_id' => $data['cid']));;
                $hash = 'd.' . str_pad((string) $size, 20, '0', STR_PAD_LEFT) . ".$data[name]";
                break;
            case _UPLOADS_VDIR_SORTBY_TYPE:
            case _UPLOADS_VDIR_SORTBY_OWNER:
            case _UPLOADS_VDIR_SORTBY_NAME:
            default:
                $hash = "d.$data[name]";
                break;
        }
        if ('||current-dir||' == $data['name'] || '||parent-dir||' == $data['name']) {
            $hash = $data['name'];
        }

        $list[$hash]['name']     = $data['name'];
        $list[$hash]['id']       = $data['cid'];
        $list[$hash]['type']     = 'application/directory';
        $list[$hash]['link']     = xarModURL('uploads', 'user', 'file_browser', array('vdir_id' => $data['cid']));
        $list[$hash]['comment']  = $data['description'];
        $list[$hash]['owner']    = 0;
        $list[$hash]['size']     = $sizeArray;
        $list[$hash]['creation'] = 0;

        unset($directories[$entry]);
    }

    foreach ($files as $entry => $data) {

        switch($sortby) {
            case _UPLOADS_VDIR_SORTBY_CREATION:
                $hash = "f.$data[id]";
                break;
            case _UPLOADS_VDIR_SORTBY_TYPE:
                $hash = "f.{$data['mimetype']['text']}.{$data['name']}";
                break;
            case _UPLOADS_VDIR_SORTBY_SIZE:
                $size = (string) $data['size']['value'];
                $hash = 'f.' . str_pad((string) $size, 20, '0', STR_PAD_LEFT);
                break;
            case _UPLOADS_VDIR_SORTBY_OWNER:
                $hash = "f.{$data['owner']['name']}";
                break;
            case _UPLOADS_VDIR_SORTBY_NAME:
            default:
                $hash = "f.$data[fileName]";
                break;
        }


        $list[$hash]['name']     = $data['name'];
        $list[$hash]['id']       = $data['id'];
        $list[$hash]['location'] = $data['location']['virtual'];
        $list[$hash]['type']     = $data['mimetype'];
        $list[$hash]['link']     = xarModURL('uploads', 'user', 'download', array('fileId' => $data['id']));
        $list[$hash]['comment']  = '';
        $list[$hash]['owner']    = $data['owner']['name'];
        $list[$hash]['size']     = $data['size']['text'];
        $list[$hash]['creation'] = 0;

        unset($files[$entry]);
    }

    $parentDir  = $list['||parent-dir||'];  unset($list['||parent-dir||']);
    $currentDir = $list['||current-dir||']; unset($list['||current-dir||']);

    if (!isset($list) || empty($list)) {
        $list = array();
    } else {
        switch($direction) {
            case _UPLOADS_VDIR_SORT_DESC:
                uksort($list, 'strnatcmp');
                $list = array_reverse($list);
                break;
            case _UPLOADS_VDIR_SORT_ASC:
            default:
                uksort($list, 'strnatcmp');
                break;
        }
    }

    return array('currentDir' => $currentDir,
                 'parentDir'  => $parentDir,
                 'list'       => $list);
}

?>
