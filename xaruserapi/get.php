<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * get a rating for a specific item
 * @param $args['modname'] name of the module this rating is for
 * @param $args['itemtype'] item type (optional)
 * @param $args['objectid'] ID of the item this rating is for
 * @return int rating the corresponding rating, or void if no rating exists
 */
function ratings_userapi_get($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($modname)) ||
        (!isset($objectid))) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('module name or item id'), 'user', 'get', 'ratings');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('module id'), 'user', 'get', 'ratings');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    // Security Check
    if(!xarSecurityCheck('ReadRatings')) return;

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ratingstable = $xartable['ratings'];
    // Get items
    $query = "SELECT xar_rating
            FROM $ratingstable
            WHERE xar_moduleid = ?
              AND xar_itemid = ?
              AND xar_itemtype = ?";
    $bindvars = array($modid, $objectid, $itemtype);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    $rating = $result->fields[0];
    $result->close();
    return $rating;
}
?>