<?php
/**
 * File: $Id: additems.php,v 1.4 2003/12/22 07:12:49 garrett Exp $
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
 */function addressbook_adminapi_addItems($args)
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


        $name = xarVarPrepForStore($name);

        $sql = "INSERT INTO $table
                            (nr,name)
                     VALUES ($nextID,'$name')";

        $result =& $dbconn->Execute($sql);
        if (!$result) $returncode = FALSE;
    }

    return $returnCode;

} // END addItems

?>