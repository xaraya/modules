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
    Check if a record exists for a xaraya module item

    @param $args['modid'] (required)
    @param $args['itemtype'] (optional)
    @param $args['itemid'] (required)

    @return boolean true if exists otherwise false
*/
function owner_userapi_ownerexists($args)
{
    $result = xarModAPIFunc('owner', 'user', 'get', $args);

    if( count($result) > 0 )
        return true;

    return false;
}
?>