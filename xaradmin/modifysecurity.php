<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    Used to Modify security levels
        Hook for display GUI

    @return string module output
*/
function security_admin_modifysecurity($args)
{
    extract($args);

    /*
        Process the std. hook info
    */
    if( !empty($extrainfo['module']) )
        $modid = xarModGetIdFromName($extrainfo['module']);

    $itemtype = 0;
    if( !empty($extrainfo['itemtype']) )
        $itemtype = $extrainfo['itemtype'];

    $itemid = 0;
    if( !empty($objectid) ){ $itemid = $objectid; }

    if( !empty($extrainfo['returnurl']) )
        $returnUrl = $extrainfo['returnurl'];
    else
        $returnUrl = xarServerGetCurrentURL();

    /*
        If user has SECURITY_ADMIN level or is a site admin let them
        modify security otherwise don't
    */
    $has_admin_security = Security::check(SECURITY_ADMIN, $modid, $itemtype, $itemid, false);
    if( !$has_admin_security ){ return ''; }

    $data = array();
    //$settings = SecuritySettings::factory($modid, $itemtype);

    // Make sure their are levels if not quit
    $args = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid);
    $data['security'] = xarModAPIFunc('security', 'user', 'get', $args);


    $groups = xarModAPIFunc('roles', 'user', 'getallgroups');
    $data['groups'] = array(0 => array('name' => xarML('All Roles')));
    foreach( $groups as $key => $group ){ $data['groups'][$group['uid']] = $group; }

    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    $data['itemid'] = $itemid;

    return xarTplModule('security', 'admin', 'modifysecurity', $data);
}
?>