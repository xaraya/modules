<?php
/**
 * File: $Id: addcustomfields.php,v 1.5 2004/01/24 18:36:21 garrett Exp $
 *
 * AddressBook admin addCustomFields
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
 * Inserts a record into the customfields table
 *
 * @param args['inserts'] array of strings
 * @return bool
 */
function addressbook_adminapi_addCustomfields($args)
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
    if(!isset($inserts)) {
        $invalid[] = 'inserts';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ', $invalid), 'admin', 'updateitems', __ADDRESSBOOK__);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        $returnCode = FALSE;
    } else {
        $dbconn =& xarDBGetConn();
        foreach($inserts as $insert) {
            $result =& $dbconn->Execute($insert);
            if (!$result) $returnCode = FALSE;

        }
    }

    return $returnCode;
} // END addCustomFields

?>