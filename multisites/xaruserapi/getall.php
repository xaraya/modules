<?php
/**
 * File: $Id$
 *
 * Xaraya Multisites
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Multisites Module
 * @author
*/

/**
 * get all subsites in database
 * @returns array
 * @return array of subsites, or false on failure
 */
function multisites_userapi_getall($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    if (!isset($where)) {
        $where = '';
    }

    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid - function getall() in module Multisites');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    // Security Check
    if(!xarSecurityCheck('ReadMultisites')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $multisitestable = $xartable['multisites'];


   // Get subsites
   $query = "SELECT  xar_msid,
                     xar_mssite,
                     xar_msprefix,
                     xar_msdb,
                     xar_msshare,
                     xar_msstatus
            FROM " . $multisitestable . " " . $where
        . " ORDER BY xar_msid";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($msid, $mssite, $msprefix, $msdb, $msshare, $msstatus) = $result->fields;
    	if(xarSecurityCheck('ReadMultisites')) {
        $items[] = array('msid'     => $msid,
                       'mssite'   => $mssite,
                       'msprefix' => $msprefix,
                       'msdb'     => $msdb,
                       'msshare'  => $msshare,
                       'msstatus' => $msstatus);
        }
    }

    $result->Close();

    return $items;
}
?>
