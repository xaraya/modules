<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Owner Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
 * Interface to see who the owner of an item is.
 *
 * @param $args array standard xaraya hook params
 * @return array standard xaraya hook extrainfo
 */
function owner_admin_changeowner($args)
{
    extract($args);

    if( !xarSecurityCheck('ChangeOwner', 0) ) return '';

    // setup vars
    $modid = '';
    if( !empty($extrainfo['module']) )
    {
        $modid = xarModGetIdFromName($extrainfo['module']);
    }

    $itemtype = '';
    if( !empty($extrainfo['itemtype']) )
    {
        $itemtype = $extrainfo['itemtype'];
    }

    $itemid = '';
    if( !empty($objectid) )
    {
        $itemid = $objectid;
    }

    $args = array(
        'modid'    => $modid,
        'itemtype' => $itemtype,
        'itemid'   => $itemid
    );
    $owner = xarModAPIFunc('owner', 'user', 'get', $args);

    if( empty($owner) ) return '';

    // Get vars ready for the template
    $owner['name'] = xarUserGetVar('name', $owner['uid']);
    $data['owner'] = $owner;

    return $data;
}
?>