<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 * 
 * Xaraya Blcaklist
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Referer Module
 * @author John Cox et al. 
 */
function comments_userapi_get_blacklist($args)
{ 
    extract($args); 
    // Optional arguments.
    if (empty($startnum)) {
        $startnum = 1;
    } 
    if (!isset($numitems)) {
        $numitems = 5000;
    } 
    $items = array(); 
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $btable = $xartable['blacklist'];
    $query = "SELECT xar_id,
                     xar_domain
              FROM $btable";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return; 
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($id, $domain) = $result->fields;
            $items[] = array('id'       => $id,
                             'domain'   => $domain);
    } 
    $result->Close(); 
    return $items;
}
?>