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
 * Delete a tag type
 *
 * @public
 * @author Richard Cave 
 * @param $args['id'] ID of the tag type
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, MISSING_DATA
 */
function html_adminapi_deletetype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     'id', 'adminapi', 'deletetype', 'html');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // The user API function is called
    $type = xarModAPIFunc('html',
                          'user',
                          'gettype',
                          array('id' => $id));

    if ($type == false) {
        $msg = xarML('No Such tag type present');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }

    // Security Check
	if(!xarSecurityCheck('DeleteHTML')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set table name
    $htmltable = $xartable['html'];
    $htmltypestable = $xartable['htmltypes'];

    // Delete the tag type
    $query = "DELETE FROM $htmltypestable
              WHERE xar_id = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Also delete the associated tags from the xar_html table
    $query = "DELETE FROM $htmltable
              WHERE xar_tid = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;


    // If this is a tag type HTML, then
    // also delete the tags from the config vars
    if ($type['type'] == 'html') {
        $allowedhtml = array();

        // Set the config vars to an empty array
        xarConfigSetVar('Site.Core.AllowableHTML', $allowedhtml);
    }

    // Let any hooks know that we have deleted a tag type
    xarModCallHooks('item', 'deletetype', $id, '');

    // Let the calling process know that we have finished successfully
    return true;
}

?>
