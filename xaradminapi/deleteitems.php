<?php
/**
 * AddressBook admin deleteItems
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Deletes a groupd of comma delimited keys from a table
 *
 * @param args['delete'] string
 * @return bool
 */
function addressbook_adminapi_deleteItems($args)
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
    if(!isset($delete)) {
        $invalid[] = 'delete';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ', $invalid), 'admin', 'updateItems', 'addressbook');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        $returnCode = FALSE;
    } else {
        $dbconn =& xarDBGetConn();
        $result =& $dbconn->Execute($delete);
        if(!$result) $returnCode = FALSE;
    }

    return $returnCode;
} // END deleteItems

?>
