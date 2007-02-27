<?php
/**
 * AddressBook admin incCustomFields
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
 * Move custom field position up one level
 *
 * @param passed in from updatecustomfields api
 * @return bool
 */
function addressbook_adminapi_incCustomfields($args)
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
    if(!isset($id) && is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ', $invalid), 'admin', 'updateItems', 'addressbook');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        $returnCode = FALSE;
    } else {

        $dbconn =& xarDBGetConn();
        $xarTables =& xarDBGetTables();
        $cus_table = $xarTables['addressbook_customfields'];

        // Get info on current position of field
        $sql = "SELECT position
                  FROM $cus_table
                 WHERE nr= ?";
        $bindvars=array((int)$id);
        $result =& $dbconn->Execute($sql,$bindvars);

        if (!$result) {
            $returnCode = FALSE;
        } elseif ($result->EOF) {
            xarErrorSet(XAR_USER_EXCEPTION, _AB_ERROR_DEBUG,
                        new abUserException(xarML("No such field ID $id")));
            $returnCode = FALSE;
        } else {
            list($seq) = $result->fields;
            $result->Close();

            // Get info on displaced field
            $sql = "SELECT nr,
                           position
                      FROM $cus_table
                     WHERE position < ?
                     ORDER BY position DESC";
            $bindvars = array ($seq);
            $result =& $dbconn->SelectLimit($sql, 1, -1, $bindvars);

            if (!$result) {
                $returnCode = FALSE;
            } elseif ($result->EOF) {
                xarErrorSet(XAR_USER_EXCEPTION, _AB_ERROR_DEBUG,
                            new abUserException(xarML("No field directly above that one")));
                return $returnCode;
            } else {
                list($altid, $altseq) = $result->fields;
                $result->Close();

                // Swap sequence numbers
                $sql = "UPDATE $cus_table
                        SET position=$seq
                        WHERE nr=$altid";
                $dbconn->Execute($sql);
                if (!$result) {
                    $returnCode = FALSE;
                } else {

                    $sql = "UPDATE $cus_table
                            SET position=$altseq
                            WHERE nr=$id";
                    $dbconn->Execute($sql);
                    if (!$result) {
                        $returnCode = FALSE;
                    }
                }
            }
        }
    }

    return $returnCode;

} // END incCustomFields

?>
