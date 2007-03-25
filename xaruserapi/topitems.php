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
 * get the list of items with top N ratings for a module
 *
 * @param $args['modname'] name of the module you want items from
 * @param $args['itemtype'] item type (optional)
 * @param $args['numitems'] number of items to return
 * @param $args['startnum'] start at this number (1-based)
 * @return array of array('itemid' => $itemid, 'hits' => $hits)
 */
function ratings_userapi_topitems($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($modname)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('module name'), 'user', 'topitems', 'ratings');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('module id'), 'user', 'topitems', 'ratings');
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
    $query = "SELECT xar_itemid, xar_rating
            FROM $ratingstable
            WHERE xar_moduleid = ?
              AND xar_itemtype = ?
            ORDER BY xar_rating DESC";
    $bindvars = array($modid, $itemtype);
    if (!isset($numitems) || !is_numeric($numitems)) {
        $numitems = 10;
    }
    if (!isset($startnum) || !is_numeric($startnum)) {
        $startnum = 1;
    }

    //$result =& $dbconn->Execute($query);
    $result = $dbconn->SelectLimit($query, $numitems, $startnum - 1, $bindvars);
    if (!$result) return;

    $topitems = array();
    while (!$result->EOF) {
        list($id,$rating) = $result->fields;
        $topitems[] = array('itemid' => $id, 'rating' => $rating);
        $result->MoveNext();
    }
    $result->close();
    return $topitems;
}
?>