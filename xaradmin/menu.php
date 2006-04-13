<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
    generate menu fragments
    @param $args['page'] - func calling menu (ex. main, view, etc)
    @return Menu template data
 */
function helpdesk_admin_menu()
{
    if (!xarSecurityCheck('readhelpdesk')) return;

    xarVarFetch('func',      'str', $data['page'],       'main', XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',  'str', $data['itemtype'],   10,     XARVAR_NOT_REQUIRED);
    xarVarFetch('selection', 'str', $data['selection'],  '',     XARVAR_NOT_REQUIRED);

    $data['userisloggedin'] = xarUserIsLoggedIn();
    $data['enabledimages']  = xarModGetVar('helpdesk', 'Enable Images');
    $modid = xarModGetIDFromName('helpdesk');

     // Get objects to build tabs
    $objects = xarModAPIFunc('dynamicdata', 'user', 'getObjects');
    $data['menulinks'] = array();
    foreach($objects as $object){
        if($object['moduleid'] == $modid && $object['itemtype'] != 1){
            $data['menulinks'][] = $object;
        }
    }

    xarTplAddStyleLink('helpdesk', 'style', $fileExt = 'css');

    return xarTplModule('helpdesk', 'admin', 'menu', $data);
}
?>
