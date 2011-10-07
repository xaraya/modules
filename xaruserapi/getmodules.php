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
 * get the list of modules for which we're rating items
 *
 * @return array $array[$modid][$itemtype] = array('items' => $numitems,'ratings' => $numratings);
 */
function ratings_userapi_getmodules($args)
{
    // Security Check
    if (!xarSecurityCheck('OverviewRatings')) return;

    // Database information
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $ratingstable = $xartable['ratings'];

    // Get items
    $query = "SELECT module_id, itemtype, COUNT(itemid), SUM(numratings)
            FROM $ratingstable
            GROUP BY module_id, itemtype
            ORDER BY module_id, itemtype";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numitems,$numratings) = $result->fields;
        $modlist[$modid][$itemtype] = array('items' => $numitems, 'ratings' => $numratings);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
