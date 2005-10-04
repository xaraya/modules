<?php

function filemanager_adminapi_updateconfighook($args)
{
    //security check
    if (!xarSecurityCheck('AdminFileManager')) return;

    // extract args out of an array and into local name space
    extract($args);

    // make sure we have an array for the calls below
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modName = xarModGetName();
    } else {
        $modName = $extrainfo['module'];
    }

    // get the modID & itemType for the modSets below, this will allow multiple
    // instances of filemanager on a page/pubtype
    $modId = xarModGetIDFromName($modName);
    $itemType = (isset($args['extrainfo']['itemtype'])?$args['extrainfo']['itemtype']:0);

    if (!xarVarFetch('mimetype',        'int:0:',               $data['mimetype'],      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('mimesubtype',     'int:0:',               $data['mimesubtype'],   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('filenamewith',    'pre:str:1:255',        $data['filenamewith'],  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('maxattachmnts',   'int:1:',               $data['maxattachmnts'], NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('displayas',       'enum:icons:list:raw:', $data['displayas'],     NULL, XARVAR_DONT_SET)) {return;}

    xarModSetVar('filemanager',"settings.attachment.$modId.$itemType", serialize($data));

    return true;
}

?>