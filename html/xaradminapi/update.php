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
 * Update an html tag
 *
 * @public
 * @author John Cox 
 * @author Richard Cave 
 * @param $args['cid'] the ID of the html tag
 * @param $args['allowed'] the flag for the html tag
 * @raise BAD_PARAM
 */
function html_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($allowed) || !is_numeric($allowed)) {
        $invalid[] = 'allowed';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     join(', ',$invalid), 'adminapi', 'update', 'html');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security Check
	if(!xarSecurityCheck('EditHTML')) return;
    
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $htmltable = $xartable['html'];

    // Update the html tag
    $query = "UPDATE $htmltable
              SET   xar_allowed = " . xarVarPrepForStore($allowed) . "
              WHERE xar_cid = " . xarVarPrepForStore($cid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a html tag
    xarModCallHooks('item', 'update', $cid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

?>
