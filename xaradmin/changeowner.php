<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package Xaraya Modules
 * @copyright (C) 2003-2005 by Envision Net, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.envisionnet.net/
 *
 * @subpackage Owner module
 * @link http://www.envisionnet.net/home/products/security/
 * @author Brian McGilligan <brian@envisionnet.net>
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