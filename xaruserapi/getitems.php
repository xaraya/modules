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
 * get a rating for a list of items
 *
 * @param $args['modname'] name of the module you want items from, or
 * @param $args['modid'] module id you want items from
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item IDs
 * @param $args['sort'] string sort by itemid (default), rating or numratings
 * @return array $array[$itemid] = array('numratings' => $numratings, 'rating' => $rating)
 */
function ratings_userapi_getitems($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($modname) && !isset($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('module name'), 'user', 'getitems', 'ratings');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!empty($modname)) {
        $modid = xarModGetIDFromName($modname);
    }
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('module id'), 'user', 'getitems', 'ratings');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    // Bug 5856: is this needed?
    if (!isset($itemtype)) {
        $itemtype = 0;
    }
    if (empty($sort)) {
        $sort = 'itemid';
    }

    // Security Check
    if(!xarSecurityCheck('ReadRatings')) return;

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ratingstable = $xartable['ratings'];

    // Get items
    $query = "SELECT xar_itemid, xar_rating, xar_numratings
            FROM $ratingstable
            WHERE xar_moduleid = ?
              AND xar_itemtype = ?";

    $bindvars[] = (int) $modid;
    $bindvars[] = (int) $itemtype;

    if (isset($itemids) && count($itemids) > 0) {
        $allids = join(', ',$itemids);
        $query .= " AND xar_itemid IN (?)";
        $bindvars[] = $allids;
    }
    if ($sort == 'rating') {
        $query .= " ORDER BY xar_rating DESC, xar_numratings DESC";
    } elseif ($sort == 'numratings') {
        $query .= " ORDER BY xar_numratings DESC, xar_rating DESC";
    } else {
        $query .= " ORDER BY xar_itemid ASC";
    }

    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    $getitems = array();
    while (!$result->EOF) {
        list($id,$rating,$numratings) = $result->fields;
        $getitems[$id] = array('numratings' => $numratings, 'rating' => $rating);
        $result->MoveNext();
    }
    $result->close();

    return $getitems;
}

?>
