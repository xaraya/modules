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
 * Get a blacklist entry or entries
 * 
 * @access public
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @param    array      $args	Hashed array of arguments with 'name' => 'value' relationships 
 * <pre>
 *		<em>mixed  </em> <strong>id      </strong> single id, or array of domain pattern ids to get
 *		<em>integer</em> <strong>startnum</strong> pagination start number
 *		<em>integer</em> <strong>numitems</strong> number of items to retrieve 
 * </pre>
 * @returns array list of blacklisted domain patterns
 */ 
function blacklist_adminapi_get( $args ) 
{ 
    extract($args); 

    $where    = '';
    $bindvars = array();

    $blCache = xarVarGetCached('blacklist', 'blCache');
    if (!isset($blCache) || empty($blCache)) {
        $blCache = array();
    }

	// If 'id' is supplied and it is an array of ids, we grab only those ids. If it is
	// specified and it is only a single entry, we grab just that, otherwise, grab all
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

    if (empty($startnum)) {
        $startnum = 1;
    } 
    if (!isset($numitems)) {
        $numitems = 25;
    } 
    $items = array(); 

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $blTable = $xartable['blacklist'];

	
    $query = "SELECT xar_id AS id, 
                     xar_domain AS domain
                FROM $blTable
			  $where";

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return; 

	while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
		$blackList[$row['id']] = $row['domain'];
        $result->MoveNext();
    }
    $result->Close();

    return $blackList;
}
?>
