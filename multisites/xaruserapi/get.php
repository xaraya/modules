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
 * get a specific subsite data
 * @param $args['msid'] id of subsite to retrieve
 * @returns array
 * @return subsite array, or false on failure
 */
function multisites_userapi_get($args)
{
    extract($args);
    if (!isset($msid)) {
        $msg = xarML('Invalid Parameter Count',
                    'userapi', 'get', 'multisites');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $multisitestable = $xartable['multisites'];

    // Get link
    $query = "SELECT xar_msid,
                     xar_mssite,
                     xar_msprefix,
                     xar_msdb,
                     xar_msshare,
                     xar_msstatus
            FROM $multisitestable
            WHERE xar_msid = " . xarVarPrepForStore($msid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($msid, $mssite, $msprefix, $msdb, $msshare, $msstatus) = $result->fields;
    $result->Close();

    // Security Check
    if(!xarSecurityCheck('ReadMultisites')) return;

    $subsite  = array('msid'     => $msid,
                     'mssite'   => $mssite,
                     'msprefix' => $msprefix,
                     'msdb'     => $msdb,
                     'msshare'  => $msshare,
                     'msstatus' => $msstatus);

    return $subsite;
}
?>
