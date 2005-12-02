<?php
/**
 * File: $Id:
 * 
 * Get all module items
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
/**
 * get all Bible texts
 * 
 * @author curtisdf 
 * @param state $ (optional) the state of texts to get
 * @param numitems $ the number of texts to retrieve (default -1 = all)
 * @param startnum $ start with this text number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_getall($args)
{ 
    extract($args); 

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    if (!isset($order)) {
        $order = 'tid';
    }
    if (!isset($sort)) {
        $sort = 'asc';
    }

    // Argument check
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (isset($state) && !is_numeric($state)) {
        $invalid[] = 'state';
    }
    if (isset($order) && !in_array($order, array('tid', 'sname', 'lname'))) {
        $invalid[] = 'order';
    }
    if (isset($sort) && ($sort != 'asc' && $sort != 'desc')) {
        $invalid[] = 'sort';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 

    $texts = array(); 

    // security check
    if (!xarSecurityCheck('ViewBible')) return; 

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables(); 

    $texttable = $xartable['bible_texts']; 

    $query = "SELECT xar_tid,
                     xar_sname,
                     xar_lname,
                     xar_file,
                     xar_md5,
                     xar_config_exists,
                     xar_md5_config,
                     xar_state,
                     xar_type
            FROM $texttable WHERE 1 ";
    $bindvars = array();
    if (isset($state)) {
        $query .= "AND xar_state = ? ";
        $bindvars[] = $state;
    }
    if (isset($type)) {
        $query .= "AND xar_type = ? ";
        $bindvars[] = $type;
    }

    $query .= "ORDER BY xar_$order $sort ";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars); 

    if (!$result) return; 

    for (; !$result->EOF; $result->MoveNext()) {
        list($tid, $sname, $lname, $file, $md5, $config_exists, $md5_config, $state) = $result->fields;
        if (xarSecurityCheck('ViewBible', 0, 'Text', "$sname:$tid")) {
            $texts[$tid] = array('tid' => $tid,
                'sname' => $sname,
                'lname' => $lname,
                'file' => $file,
                'md5' => $md5,
                'config_exists' => $config_exists,
                'md5_config' => $md5_config,
                'state' => $state);
        }
    }
    $result->Close(); 

    // Return the texts
    return $texts;
} 

?>
