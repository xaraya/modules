<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Get a specific html tag
 *
 * @public
 * @author John Cox 
 * @purifiedby Richard Cave 
 * @param $args['cid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 * @raise BAD_PARAM
 */
function html_userapi_get($args)
{
    // Extract arguments
    extract($args);

    // Argument check
    if (!isset($cid) && !is_numeric($cid)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     'cid', 'userapi', 'get', 'html');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
	if(!xarSecurityCheck('ReadHTML')) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $htmltable = $xartable['html'];

    // Get link
    $query = "SELECT xar_cid,
                     xar_tag,
                     xar_allowed
              FROM $htmltable
              WHERE xar_cid = " . xarVarPrepForStore($cid);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($cid, $tag, $allowed) = $result->fields;
    $result->Close();

    $html = array('cid'      => $cid,
                  'tag'      => $tag,
                  'allowed'  => $allowed);

    return $html;
}

?>
