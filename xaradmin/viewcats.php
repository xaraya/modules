<?php

/**
 * create item from xarModFunc('categories','admin','viewcat')
 */
function categories_admin_viewcats()
{
    // Get parameters
    if(!xarVarFetch('activetab',    'isset', $activetab,    0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('startnum',     'isset', $data['startnum'],    1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('itemsperpage',   'isset', $data['itemsperpage'],    xarModVars::get('categories', 'itemsperpage'), XARVAR_NOT_REQUIRED)) {return;}

    // Security check
    if(!xarSecurityCheck('ManageCategories')) return;

    $data['options'][] = array('cid' => $activetab);

    if (!isset($useJSdisplay)) {
        $useJSdisplay = $data['useJSdisplay'] = xarModVars::get('categories','useJSdisplay');
    } else {
        $data['useJSdisplay'] = $useJSdisplay;
    }

    return xarTplModule('categories','admin','viewcats-render',$data);
}

?>
