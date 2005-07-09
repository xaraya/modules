<?php
/**
    generate menu fragments
    @param $args['page'] - func calling menu (ex. main, view, etc)
    @return Menu template data
 */
function helpdesk_admin_menu()
{
    if (!xarSecurityCheck('readhelpdesk')) return;

    xarVarFetch('func',      'str', $data['page'],       'main', XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',  'str', $data['itemtype'],   1,      XARVAR_NOT_REQUIRED);
    xarVarFetch('selection', 'str', $data['selection'],  '',     XARVAR_NOT_REQUIRED);

    $data['userisloggedin'] = xarUserIsLoggedIn();
    $data['enabledimages']  = xarModGetVar('helpdesk', 'Enable Images');
    $modid = xarModGetIDFromName('helpdesk');

     // Get objects to build tabs
    $objects = xarModAPIFunc('dynamicdata', 'user', 'getObjects');
    $data['menulinks'] = array();
    foreach($objects as $object){
        if($object['moduleid'] == $modid){
            $data['menulinks'][] = $object;
        }
    }

    xarTplAddStyleLink('roles',    'tabs',  $fileExt = 'css');
    xarTplAddStyleLink('helpdesk', 'style', $fileExt = 'css');

    return xarTplModule('helpdesk', 'admin', 'menu', $data);
}
?>
