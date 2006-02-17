<?php
/**
 * Get a specific person id
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author Michel V.
 */
/**
 * get a specific person id by parameters
 *
 * @author the Michel V.
 * @param $args ['uid'] uid of sigmapersonnel item to get
 * @param $args ['firstname'] ?First name of person to get?
 * @returns array
 * @return array $item ['personid', ['persstatus'], or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function sigmapersonnel_userapi_getpersonid($args)
{
    extract($args);
    if (!xarVarFetch('uid', 'int:1:', $userid, xarUserGetVar('uid'), XARVAR_NOT_REQUIRED)) return;

    /*
    // Argument check
    if (!isset($personid) || !is_numeric($personid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'SIGMA Personnel');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    */
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sigmapersonneltable = $xartable['sigmapersonnel_person'];
    // Get item
    $query = "SELECT xar_personid,
                     xar_persstatus
            FROM $sigmapersonneltable
            WHERE xar_userid = ?";
    $result = &$dbconn->Execute($query,array($userid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        /* $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg)); */
        return;
    }
    // Obtain the item information from the result set
    list($personid, $persstatus) = $result->fields;

    $result->Close();
    // Security check
    if (!xarSecurityCheck('ReadSIGMAPersonnel', 1, 'PersonnelItem', "$personid:All:$persstatus")) {
        return;
    }
    // Create the item array
    $item = array(
        'personid' => $personid,
        'persstatus' => $persstatus);
    // Return the item array
    return $item;
}

?>
