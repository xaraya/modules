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
function security_admin_changesecurity($args)
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

//    $has_security = xarModAPIFunc('security', 'user', 'securityexists',
//        array(
//            'modid'    => $modid,
//            'itemtype' => $itemtype,
//            'itemid'   => $itemid
//        )
//    );

    xarModAPILoad('security');
    $settings = SecuritySettings::factory($modid, $itemtype);

    /*
        Get all the current security and owner info
    */
    // Make sure their are levels if not quit
    $args = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid);
    $security = xarModAPIFunc('security', 'user', 'get', $args);

    // Make user this has an owner otherwise quit
    if( empty($settings->owner_table) )
    {
        $owner = xarModAPIFunc('owner', 'user', 'get', $args);
        //if( !$owner ) return '';
    }
    else
    {
        // Use owner table field settings to extract the owner from the database
        $dbconn   =& xarDBGetConn();
        $sql = "
            SELECT {$settings->owner_column}
            FROM {$settings->owner_table}
            WHERE {$settings->owner_primary_key} = ?
        ";
        $result = $dbconn->Execute($sql, array($itemid));
        if( !$result ){ return false; }
        $owner['uid'] = $result->fields[0];
    }

    $groups = xarModAPIFunc('roles', 'user', 'getallgroups');

    $tmp = array();
    foreach( $groups as $key => $group ){ $tmp[$group['uid']] = $group; }
    $groups = $tmp;

    $secLevels = xarModAPIFunc('security', 'user', 'getlevels');

    /*
        Setup vars for the template
    */
    $data['standalone'] = true;
    if( xarRequestGetVar('type') == 'admin' || xarRequestGetVar('func') == 'modify' )
    {
        $data['standalone'] = false;
    }
    $data['settings'] = $settings;
    $data['owner']    = $owner['uid'];
    $data['sec_levels']= $secLevels; // different security levels
    $data['security']   = $security; // Sec levels for each group
    $data['all_groups']   = $groups; // Groups user is in
    $data['modid']    = $modid;
    $data['itemtype'] = $itemtype;
    $data['itemid']   = $itemid;
    $data['action']   = xarModURL('security', 'admin', 'creategroupsecurity');
    $data['returnurl']= $returnUrl;

    return xarTplModule('security', 'admin', 'changesecurity', $data);
}
?>