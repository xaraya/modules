<?php
/**
 * File: $Id: updateitems.php,v 1.7 2004/01/24 18:36:22 garrett Exp $
 *
 * AddressBook adminapi updateItems()
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
 * Updates one or more rows, individually based on the complete SQL string
 * passed via $updates
 *
 * @param args['tablename'] string
 * @param args['updates'] array of strings
 * @return true
 */
 function addressbook_adminapi_updateItems($args) {

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
    if(!isset($tablename) || !is_string($tablename)) {
        $invalid[] = 'tablename';
    }
    if(!isset($updates) || !is_array($updates)) {
        $invalid[] = 'updates';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ', $invalid), 'admin', 'updateItems', __ADDRESSBOOK__);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        $returnCode = FALSE;
    } else {
        $dbconn =& xarDBGetConn();
        foreach($updates as $update) {

            $result =& $dbconn->Execute($update);
            if (!$result) $returnCode = FALSE;
        }
    }

    return $returnCode;

} // END updateItems

?>