<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 * 
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
    // Argument check
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    } 
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    } 
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'Example');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
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
            WHERE xar_url != 'Bookmark' 
            ORDER BY xar_time
            DESC";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1); 
    // Check for an error
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