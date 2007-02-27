<?php
/**
 * AddressBook admin resequenceCustomFields
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
 * Resequence custom field position ids
 *
 * @param none
 * @return bool
 */
function addressbook_adminapi_resequenceCustomfields()
{

    /**
     * Security check
     */
    if (!xarSecurityCheck('AdminAddressBook',0)) return FALSE;

    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $cus_table = $xarTables['addressbook_customfields'];

    // Get the information
    $query = "SELECT nr, position
                FROM $cus_table
               ORDER BY position";
    $result =& $dbconn->Execute($query);
    if (!$result) return FALSE;

    // Fix sequence numbers
    $seq=1;
    while(list($id, $curseq) = $result->fields) {
        $result->MoveNext();
        if ($curseq != $seq) {
            $query = "UPDATE $cus_table
                         SET position= ?
                       WHERE nr= ?";
            $bindvars=array($seq,$id);
            $result =& $dbconn->Execute($query,$bindvars);
            if (!$result) return FALSE;
        }
        $seq++;
    }
    $result->Close();

    return TRUE;

} // END resequenceCustomFields

?>
