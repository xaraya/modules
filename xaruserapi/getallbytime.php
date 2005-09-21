<?php
/**
 * Xaraya Referers
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Referer Module
 * @author John Cox et al. 
 */
function referer_userapi_getallbytime($args)
{ 
    // Get arguments
    extract($args); 
    // Optional arguments.
    if ($startnum == "") {
        $startnum = 1;
    } 
    if (!isset($numitems)) {
        $numitems = -1;
    } 

    $items = array(); 
    // Security Check
    if (!xarSecurityCheck('OverviewReferer')) return; 
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $referertable = $xartable['referer'];

    $query = "SELECT xar_rid,
                   xar_url,
                   xar_frequency
            FROM $referertable
            WHERE xar_url != ? 
            ORDER BY xar_time
            DESC";
    $bind[] = 'Bookmark';
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bind);
    if (!$result) return; 
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid, $url, $frequency) = $result->fields;
        if (xarSecurityCheck('OverviewReferer')) {
            $items[] = array('rid' => $rid,
                'url' => $url,
                'frequency' => $frequency);
        } 
    } 

    $result->Close(); 
    // Return the items
    return $items;
}
?>