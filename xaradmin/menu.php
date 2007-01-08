<?php
/**
 * SIGMAPersonnel menu for admin
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author SIGMAPersonnel module development team
 */
/**
 *  generate menu fragments for the admin
 *  @author MichelV <michelv@xarayahosting.nl>
 *  @param $args['page'] - func calling menu (ex. main, view, etc)
 *  @return array Menu template data
 */
function sigmapersonnel_admin_menu()
{
    if (!xarSecurityCheck('EditSIGMAPersonnel')) return;

    xarVarFetch('func',      'str', $data['page'],       'view', XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',  'str', $data['itemtype'],   3,      XARVAR_NOT_REQUIRED);
    xarVarFetch('selection', 'str', $data['selection'],  '',     XARVAR_NOT_REQUIRED);

    $data['userisloggedin'] = xarUserIsLoggedIn();
 //;   $data['enabledimages']  = xarModGetVar('courses', 'Enable Images');
    $modid = xarModGetIDFromName('sigmapersonnel');

     // Get objects to build tabs
    $objects = xarModAPIFunc('dynamicdata', 'user', 'getObjects');
    $data['menulinks'] = array();
    foreach($objects as $object){
        if($object['moduleid'] == $modid){
            $data['menulinks'][] = $object;
        }
    }

    return xarTplModule('sigmapersonnel', 'admin', 'menu', $data);
}
?>
