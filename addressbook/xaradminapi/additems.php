<?php
/**
 * File: $Id: additems.php,v 1.2 2004/03/28 23:22:58 garrett Exp $
 *
 * AddressBook admin addItems
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Inserts a record into a table that follows the convention:
 * - column: nr = code
 * - column: name = decode
 *
 * @param args['tablename'] string
 * @param args['name'] string
 * @return bool
 */
function addressbook_adminapi_addItems($args)
{
    $returnCode = TRUE;

    /**
     * Security check
     */
    if (!xarSecurityCheck('AdminAddressBook',0)) return FALSE;

    extract($args);

    /*
     * Validate parameters
     */
    $invalid = array();
    if(!isset($tablename)) {
        $invalid[] = 'tablename';
    }
    if(!isset($name)) {
        $invalid[] = 'name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ', $invalid), 'admin', 'updateitems', __ADDRESSBOOK__);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        $returnCode = FALSE;
    } else {

        $dbconn =& xarDBGetConn();
        $xarTables =& xarDBGetTables();
        $tablename = 'addressbook_'.$tablename;
        $table = $xarTables[$tablename];

        $nextID = $dbconn->GenID($table);
        $bindvars = array ($nextID,$name);

        $sql = "INSERT INTO $table
                            (nr,name)
                     VALUES (?,?)";

        $result =& $dbconn->Execute($sql,$bindvars);
        if (!$result) $returncode = FALSE;
    }

    return $returnCode;

} // END addItems

?>