<?php
/**
 * Maxercalls administration menu
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 *  generate menu fragments
 *  @param $args['page'] - func calling menu (ex. main, view, etc)
 *  @return Menu template data
 */
function maxercalls_admin_menu()
{
    if (!xarSecurityCheck('AdminMaxercalls')) return;

    xarVarFetch('func',      'str', $data['page'],       'main', XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',  'str', $data['itemtype'],   3,      XARVAR_NOT_REQUIRED);
    xarVarFetch('selection', 'str', $data['selection'],  '',     XARVAR_NOT_REQUIRED);

    $data['userisloggedin'] = xarUserIsLoggedIn();
 //;   $data['enabledimages']  = xarModGetVar('courses', 'Enable Images');
    $modid = xarModGetIDFromName('maxercalls');

     // Get objects to build tabs
    $objects = xarModAPIFunc('dynamicdata', 'user', 'getObjects');
    $data['menulinks'] = array();
    foreach($objects as $object){
        if($object['moduleid'] == $modid){
            $data['menulinks'][] = $object;
        }
    }

    return xarTplModule('maxercalls', 'admin', 'menu', $data);
}
?>
