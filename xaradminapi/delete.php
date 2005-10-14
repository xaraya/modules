<?php

/**
 * File: $Id$
 *
 * BlackList API 
 *
 * @package Modules
 * @copyright (C) 2002-2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BlackList
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
*/

/**
 * Delete a blacklist entry or entries
 * 
 * @access public
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @param    array      $args	Hashed array of arguments with 'name' => 'value' relationships 
 * <pre>
 *		<strong>integer</strong>	<em>id</em>	single id, or array of domain pattern ids
 * </pre>
 * @returns bool True on success, False otherwise
 */ 
function blacklist_adminapi_delete( $args ) 
{ 
    extract($args); 

    $bindvars = array();

	// If 'id' is supplied and it is an array of ids, we delete only those ids. If it is
	// specified and it is only a single entry, we delete just that, otherwise, return TRUE
    if (isset($id)) {
        if (is_array($id) && count($id)) {

            $list = array();

            foreach ($id as $id) {
                if (in_array($id, array_keys($blCache))) {
                    $blackList[$id] = $blCache[$id];
                } else {
                    $list[]     = '?';
                    $bindvars[] = $id;
                }
            }
			// if $list is empty, that means all the ids were
			// found in the cache, so we can now safely 
			// return the generated $blackList
            if (empty($list)) {
                return $blackList;
            } else {
                $where = 'WHERE xar_fileEntry_id IN (' . implode(',', $list) . ')';
            }
        } elseif ('all' == strtolower($id)) {
			// Set it to something to bypass the return TRUE below
			$where = ' ';
		} elseif (!empty($id)) {

            if (in_array($id, array_keys($blCache))) {
                $blackList[$id] = $blCache[$id];
                return $blackList;
            } else {
                $where = "WHERE xar_fileEntry_id = ?";
                $bindvars[] = $id;
            }
        } 
    } 

	if (!isset($where)) {
		return TRUE;
	}

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $blTable = $xartable['blacklist'];

	
    $query = "DELETE 
                FROM $blTable
			  $where";

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return; 
    $result->Close();

    return $TRUE;
}
?>
