<?php
/**
 * File: $Id:
 *
 * Delete an sigmapersonnel item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */
/**
 * delete an sigmapersonnel item
 *
 * @author the Michel V.
 * @param  $args ['exid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function sigmapersonnel_adminapi_delete($args)
{
    extract($args);
    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($personid) || !is_numeric($personid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete', 'SIGMAPersonnel');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // The user API function is called.
    $item = xarModAPIFunc('sigmapersonnel',
        'user',
        'get',
        array('personid' => $personid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('DeleteSIGMAPersonnel', 1, 'PersonnelItem', "$personid:All:$persstatus")) {
        return;
    }
    // Get database setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $sigmapersonneltable = $xartable['sigmapersonnel'];
    // Delete the item
    $query = "DELETE FROM $sigmapersonneltable WHERE xar_persid = ?";
    // The bind variable $exid is directly put in as a parameter.
    $result = &$dbconn->Execute($query,array($persid));
    if (!$result) return;

    // Let any hooks know that we have deleted an item.
    $item['module'] = 'sigmapersonnel';
    $item['itemid'] = $personid;
    xarModCallHooks('item', 'delete', $personid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>
