<?php

function filemanager_vdirapi_split_path( $args )
{
    extract($args);

    if (!isset($path)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'path', 'vdir_get_dir_list', 'filemanager');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (is_array($path)) {
        $path = '/' . implode('/', $path);
    }

    $start = 1;
    $parts = explode('/', $path);
    $pathArray = array();

    foreach ($parts as $part) {
        if (trim($part)) {
            $pathArray[$start++] = $part;
        }
    }

    if (!isset($pathArray) || !count($pathArray)) {
        return array();
    } else {
        return $pathArray;
    }
}

?>
