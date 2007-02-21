<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    Creates default security for an item if none exists

    @param $args array standard hook params

    @return $extrainfo array containing standard hooks extrainfo
*/
function security_adminapi_createhook($args)
{
    extract($args);

    xarModAPILoad('security', 'user');

    // setup vars for insertion
    if( !empty($extrainfo['module']) )
        $modid = xarModGetIdFromName($extrainfo['module']);

    $itemtype = 0;
    if( !empty($extrainfo['itemtype']) )
        $itemtype = $extrainfo['itemtype'];

    $itemid = 0;
    if( !empty($objectid) )
        $itemid = $objectid;

    /*
        Check args and set any needed exceptions
    */
    if( empty($modid) )
    {
        $msg = "Missing module id in security_adminapi_createhook";
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MISSING_PARAM', $msg);
        return false;
    }

    /*
        Get the default settings for this module / itemtype pair
    */
    $settings = SecuritySettings::factory($modid, $itemtype);
    $security = new SecurityLevels($modid, $itemtype, $itemid);
    $security->levels = $settings->default_item_levels;

    // Find owner so that we can sub user with user id
    $owner_id = xarModAPIFunc('security', 'user', 'get_owner_id',
        array(
            'modid'    => isset($modid) ? $modid : null,
            'itemtype' => isset($itemtype) ? $itemtype : null,
            'itemid'   => isset($itemid) ? $itemid : null
        )
    );
    $security->levels[$owner_id] = $settings->default_item_levels['user'];
    unset($security->levels['user']);

    /*
        Check if there are any extra security group
    */
    if( !xarVarFetch('security_select_groups', 'array', $select_groups, array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( count($select_groups) > 0 )
    {
        /*
            Extra groups is used for a helpdesk companies concept.  Where users in the
            can "company" can share a ticket, but a user can be in multiple companies.
            This is only used when you want  a group to share an item equally with the owner
        */
        foreach( $select_groups as $group )
        {
            if(
                empty($settings->exclude_groups[$group]) &&
                empty($security->levels[$group]) &&
                $group > 2 &&
                !isset($settings->exclude_groups[0])
            )
            {
                $security->levels[$group] = $settings->default_group_level;
            }
        }
    }
    else
    {
        $roles = new xarRoles();
        $user = $roles->getRole( xarUserGetVar('uid') );
        $parents = $user->getParents();
        foreach( $parents as $parent )
        {
            if(
                empty($settings->exclude_groups[$parent->uid]) &&
                empty($security->levels[$parent->uid]) &&
                $parent->uid > 2 &&
                !isset($settings->exclude_groups[0])
            )
            {
                $security->levels[$parent->uid] = $settings->default_group_level;
            }
        }
    }

    $result = Security::create($security,$modid, $itemtype, $itemid);
    if( !$result ){ return false; }

    return $extrainfo;
}

?>