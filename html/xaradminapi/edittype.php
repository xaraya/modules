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
 * Edit a tag type
 *
 * @author Richard Cave 
 * @param $args['id'] ID of the tag type to change
 * @param $args['tagtype'] the tag type
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, MISSING_DATA
 */
function html_adminapi_edittype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (!isset($tagtype) || !is_string($tagtype)) {
        $invalid[] = 'tag type';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     join(', ',$invalid), 'adminapi', 'edittype', 'html');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // The user API function is called
    $type = xarModAPIFunc('html',
                          'user',
                          'gettype',
                          array('id' => $id));

    if ($type == false) {
        $msg = xarML('No such tag  type present.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }

    // Security Check
	if(!xarSecurityCheck('EditHTML')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $htmltypestable = $xartable['htmltypes'];

    // Make sure tag type is lowercase
    $tagtype = strtolower($tagtype);

    // Update the tag type
    $query = "UPDATE $htmltypestable
              SET xar_type = '" . xarVarPrepForStore($tagtype) . "'
              WHERE xar_id = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a html
    xarModCallHooks('item', 'edittype', $id, '');

    // Let the calling process know that we have finished successfully
    return true;
}

?>
