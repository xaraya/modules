<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @html http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Delete an html tags word
 *
 * @public
 * @author John Cox 
 * @purifiedby Richard Cave 
 * @param $args['cid'] ID of the html
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, MISSING_DATA
 */
function html_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($cid) || !is_numeric($cid)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     'cid', 'adminapi', 'delete', 'html');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // The user API function is called
    $html = xarModAPIFunc('html',
                         'user',
                         'get',
                         array('cid' => $cid));

    if ($html == false) {
        $msg = xarML('No Such HTML tag Present', 'html');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }

    // Security Check
	if(!xarSecurityCheck('DeleteHTML')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $htmltable = $xartable['html'];

    // Delete the item
    $query = "DELETE FROM $htmltable
              WHERE xar_cid = " . xarVarPrepForStore($cid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Also delete the item from the config vars
    $allowedhtml = array();

    // Get the current html tags from config vars
    foreach (xarConfigGetVar('Site.Core.AllowableHTML') as $key => $value) {
        // Remove the deleted html tag from the config vars
        if ($key != $html['tag']) {
            $allowedhtml[$key] = $value;
        }
    }

    // Set the config vars
    xarConfigSetVar('Site.Core.AllowableHTML', $allowedhtml);

    // Let any hooks know that we have deleted a html
    xarModCallHooks('item', 'delete', $cid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

?>
