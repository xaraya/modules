<?php

/**
 * vdir_create - create a virtual directory
 *
 * @author  Carl P. Corliss (ccorliss@schwabfoundation.org)
 * @param   integer $parentid       Parent Directory ID
 * @param   string  $name           name of new virtual directory
 * @param   string  $description    description of the new virtual directory
 * @returns integer
 * @return  id of new directory or === FALSE on error
 */

function filemanager_vdirapi_create( $args )
{

    extract($args);

    if (!isset($parentid) || empty($parentid)) {
        $msg = xarML('Missing parameter \'#(1)\' in module \'#(2)\' function \'#(3)\'',
                     'parentid', 'filemanager', 'vdir_create');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($name) || empty($name)) {
        $msg = xarML('Missing parameter \'#(1)\' in module \'#(2)\' function \'#(3)\'',
                     'parentid', 'filemanager', 'vdir_create');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($description)) {
        $description = '';
    }
    
    xarVarSetCached('installer', 'installing', 1);
    $homeDirId =  xarModAPIFunc('categories','admin', 'create',
                          array('name'        => $name,
                                'description' => $description,
                                'parent_id'   => $parentid));
    xarVarSetCached('installer', 'installing', 0);
    return $homeDirId;
}

?>