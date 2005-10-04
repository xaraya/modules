<?php

/**
 * Return the encoded Virtual Directory Path for the specified directory
 *
 * @author  Carl P. Corliss (ccorliss@schwabfoundation.org)
 * @param   integer $dirId      ID of directory to get the path of
 * @returns string
 * @return  the string version of the path
 */

function filemanager_vdirapi_path_encode( $args )
{

    extract($args);

    if (!isset($vdir_id)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'dirId', 'vdir_path_encode', 'filemanager');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;

    }

    $vdirCache = xarVarGetCached('filemanager', 'cache.vpathEncoded');
    if (isset($vdirCache[$vdir_id])) {
        return $vdirCache[$vidr_id];
    } elseif(!isset($vdirCache) || empty($vdirCache)) {
        $vdirCache = array();
    }

    $directories = xarModAPIFunc('categories', 'user', 'getancestors',
                                  array('cid'           => $vdir_id,
                                        'self'          => TRUE,
                                        'order '        => 'self',
                                        'descendants'   => 'none'));

    if (isset($directories) && !empty($directories)) {
        $path = '';

        foreach ($directories as $entry) {
            $path .= '/' . $entry['name'];
        }
    } else {
        $path = '/';
    }

    xarVarSetCached('filemanager', 'cache.vpathEncoded', $vdirCache);
    return $path;
}

?>
