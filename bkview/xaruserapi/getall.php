<?php

/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * get all bkview items
 *
 * @author Marcel van der Boom
 * @param numitems the number of items to retrieve (default -1 = all)
 * @param startnum start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bkview_userapi_getall($args)
{
    extract($args);
    $items = array();

    if (!xarSecurityCheck('ViewAllRepositories')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $bkviewtable = $xartable['bkview'];

    $sql = "SELECT xar_repoid,
                   xar_name,
                   xar_path
            FROM $bkviewtable
            ORDER BY xar_repoid";
    $result = $dbconn->Execute($sql);
    if(!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($repoid, $reponame, $repopath) = $result->fields;
        $items[] = array('repoid' => $repoid,
                         'reponame' => $reponame,
                         'repopath' => $repopath);
        
    }
    
    $result->Close();
    return $items;
}
?>