<?php
/**
 * AddressBook admin addCustomFields
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
                     join(', ', $invalid), 'admin', 'updateitems', 'addressbook');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        $returnCode = FALSE;
    } else {
        $dbconn =& xarDBGetConn();
        foreach($inserts as $insert) {
            $result =& $dbconn->Execute($insert['sql'],$insert['bindvars']);
            if (!$result) $returnCode = FALSE;

        }
    }

    return $returnCode;
} // END addCustomFields

?>