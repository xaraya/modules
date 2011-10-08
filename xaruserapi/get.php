<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
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
 * @param $args['itemid'] ID of the item this rating is for
 * @return int rating the corresponding rating, or void if no rating exists
 */
function ratings_userapi_get($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($modname)) ||
        (!isset($itemid))) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('module name or item id'), 'user', 'get', 'ratings');
        throw new Exception($msg);
    }
    $modid = xarMod::getRegID($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('module id'), 'user', 'get', 'ratings');
        throw new Exception($msg);
    }

    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    // Security Check
    if(!xarSecurityCheck('ReadRatings')) return;

    // Database information
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $ratingstable = $xartable['ratings'];
    // Get items
    $query = "SELECT rating
            FROM $ratingstable
            WHERE module_id = ?
              AND itemid = ?
              AND itemtype = ?";
    $bindvars = array($modid, $itemid, $itemtype);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    $rating = $result->fields[0];
    $result->close();
    // Return the rating as a single number.
    // Bug 6160 requests an array with the rating and the numrating, solved by using getitems function
    return $rating;
}
?>