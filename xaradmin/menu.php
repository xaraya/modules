<?php
/**
 * Courses administration menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */

/**
 *  generate menu fragments
 *  @param $args['page'] - func calling menu (ex. main, view, etc)
 *  @return Menu template data
 */
function courses_admin_menu()
{
    if (!xarSecurityCheck('ReadCourses')) return;

    xarVarFetch('func',      'str', $data['page'],       'main', XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',  'str', $data['itemtype'],   3,      XARVAR_NOT_REQUIRED);
    xarVarFetch('selection', 'str', $data['selection'],  '',     XARVAR_NOT_REQUIRED);

    $data['userisloggedin'] = xarUserIsLoggedIn();
 //;   $data['enabledimages']  = xarModGetVar('courses', 'Enable Images');
    $modid = xarModGetIDFromName('courses');

     // Get objects to build tabs
    $objects = xarModAPIFunc('dynamicdata', 'user', 'getObjects');
    $data['menulinks'] = array();
    foreach($objects as $object){
        if($object['moduleid'] == $modid){
            $data['menulinks'][] = $object;
        }
    }

    return xarTplModule('courses', 'admin', 'menu', $data);
}
?>
