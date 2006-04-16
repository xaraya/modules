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

    $itemid = '';
    if( !empty($objectid) ){ $itemid = $objectid; }

    if( !empty($extrainfo['returnurl']) )
        $returnUrl = $extrainfo['returnurl'];
    else
        $returnUrl = xarServerGetCurrentURL();

    $data = array();

    $has_security = xarModAPIFunc('security', 'user', 'securityexists',
        array(
            'modid'    => $modid,
            'itemtype' => $itemtype,
            'itemid'   => $itemid
        )
    );
    $settings = xarModAPIFunc('security', 'user', 'get_default_settings',
        array(
            'modid'    => $modid,
            'itemtype' => $itemtype
        )
    );
    if( !$has_security )
    {
        xarModAPIFunc('security', 'admin', 'create',
            array(
                'modid'    => $modid,
                'itemtype' => $itemtype,
                'itemid'   => $itemid,
                'settings' => $settings
            )
        );
    }

    /*
        If user has SECURITY_ADMIN level or is a site admin let them
        modify security otherwise don't
    */
    xarModAPILoad('security');
    $has_admin_security = xarModAPIFunc('security', 'user', 'check',
        array(
            'modid'    => $modid,
            'itemtype' => $itemtype,
            'itemid'   => $itemid,
            'level'    => SECURITY_ADMIN,
            'hide_exception' => true
        )
    );
    if( !$has_admin_security && !xarSecurityCheck('AdminPanel', 0) ){ return ''; }

    /*
        Get all the current security and owner info
    */
    // Make sure their are levels if not quit
    $args = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid);
    $levels = xarModAPIFunc('security', 'user', 'get', $args);
    if( !$levels ) return '';

    // Make user this has an owner otherwise quit
    if( is_null($settings['owner']) )
    {
        $owner = xarModAPIFunc('owner', 'user', 'get', $args);
        if( !$owner ) return '';
    }
    else
    {
        // Use owner table field settings to extract the owner from the database
        $dbconn   =& xarDBGetConn();
        $sql = "
            SELECT {$settings['owner']['column']}
            FROM {$settings['owner']['table']}
            WHERE {$settings['owner']['primary_key']} = ?
        ";
        $result = $dbconn->Execute($sql, array($itemid));
        if( !$result ){ return false; }
        $owner['uid'] = $result->fields[0];
    }


    /*
        Get all the groups just incase it's needed for display purposes
    */
    $all_groups = xarModAPIFunc('roles', 'user', 'getallgroups');
    $groupCache = array();
    foreach( $all_groups as $key => $group )
    {
        $groupCache[$group['uid']] = $group;
    }

    /*
        If an admin allow admin to change privs as if they were the owner.
        This allows the admin to assign privs how ever they want even if the
        user can not do it.
    */
    if( xarSecurityCheck('AdminPanel', 0) ){ $uid = xarUserGetVar('uid'); }
    else{ $uid = $owner['uid']; }

    /*
        These groups are used in the Add groups menu thing to create new group privs
    */
    if( xarSecurityCheck('AdminPanel', 0) )
        $groups = $all_groups;
    else
        $groups = xarModAPIFunc('roles', 'user', 'getancestors', array('uid' => $uid));

    $tmp = array();
    foreach( $groups as $key => $group ){ $tmp[$group['uid']] = $group; }
    $groups = $tmp;

    $secLevels = xarModAPIFunc('security', 'user', 'getlevels');

    /*
        Calc Security Levels and make a Map
    */
    $secMap = array();
    foreach( $secLevels as $secLevel )
    {
        $currentLevel = $secLevel['level'];
        $tmp = $levels['user'] & $secLevel['level'];
        $secMap['user'][$currentLevel] = $levels['user'] & $currentLevel;
        $secMap['world'][$currentLevel] = $levels['world'] & $currentLevel;
        foreach( $levels['groups'] as $gid => $group )
        {
            $secMap[$gid][$currentLevel] = $group & $currentLevel;
        }
    }

    /*
        Setup vars for the template
    */
    $data['standalone'] = true;
    if( xarRequestGetVar('type') == 'admin' || xarRequestGetVar('func') == 'modify' )
    {
        $data['standalone'] = false;
    }
    $data['secLevels']= $secLevels; // different security levels
    $data['secMap']   = $secMap; // Security Map
    $data['levels']   = $levels; // Sec levels for each group
    $data['all_groups'] = $all_groups;
    $data['user_groups']   = $groups; // Groups user is in
    $data['groupCache']= $groupCache;
    $data['showRemove']= count($groupCache) > 1 ? true : false;
    $data['modid']    = $modid;
    $data['itemtype'] = $itemtype;
    $data['itemid']   = $itemid;
    $data['action']   = xarModURL('security', 'admin', 'creategroupsecurity');
    $data['returnurl']= $returnUrl;

    return $data;
}
?>