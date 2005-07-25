<?php

function uploads_admin_modifyconfighook($args)
{
    //security check
    if (!xarSecurityCheck('AdminUploads')) return;


    // extract args out of an array and into local name space
    extract($args);

    // make sure we have an array
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
    // instances of uploads on a page/pubtype
    $modId = xarModGetIDFromName($modName);
    $itemType = (isset($args['extrainfo']['itemtype'])?$args['extrainfo']['itemtype']:0);


    // get all the info out of the db/passed form and then extract it
    // make sure the xarModGetVar worked and use an empty array just in case
    $data = (xarModGetVar('uploads',"settings.attachment.$modId.$itemType")?unserialize(xarModGetVar('uploads',"settings.attachment.$modId.$itemType")):array());


    if (!xarVarFetch('mimetype',          'int:0:',               $data['mimetype'],      0,     XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('mimesubtype',     'int:0:',               $data['mimesubtype'],   0,     XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('filenamewith',    'pre:str:1:255',        $data['filenamewith'],  NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('maxattachmnts',   'int:0:',               $data['maxattachmnts'], 0,     XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('displayas',       'enum:icons:list:raw:', $data['displayas'],     'icons', XARVAR_GET_OR_POST)) {return;}

    // default itemType to 0
    $itemType = 0;

    // get the list of all the mime types
    $options  = xarModAPIFunc('uploads','user','process_filters',
                               array('mimetype'=>$data['mimetype'],
                                     'subtype'=>$data['mimesubtype']));

    // push the mime type and sub mime type into the data array
    $data['mimetypeList']       = $options['data']['filters']['mimetypes'];
    $data['submimetypeList']    = $options['data']['filters']['subtypes'];


    //prep the list of display options
    $data['displayasList']['list']  = "List";
    $data['displayasList']['icons'] = "Icons";
    $data['displayasList']['raw']   = "Raw";

    return $data;
}
?>
