<?php
/**
 * File: $Id: getitems.php,v 1.1 2003/07/08 23:56:13 garrett Exp $
 *
 * AddressBook utilapi getitems()
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
 * Retrieves a two dimensional array consiting of a key=>value pair
 * from the tablename pass via args. Used to populate SELECT dropdowns
 *
 * @author Garrett Hunter <garrett@blacktower.com>
 * @access public
 * @param args['tablename'] string
 * @return arrItems array
 */
function addressbook_utilapi_getitems($args)
{
    $arrItems = array();

    extract($args);

    /**
     * Validate parameters
     */
    $invalid = array();
    if(!isset($tablename) || !is_string($tablename)) {
        $invalid[] = 'tablename';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'getitems', __ADDRESSBOOK__);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
    } else {

        if (isset($tablename)) {
            $dbconn =& xarDBGetConn();
            $xarTables =& xarDBGetTables();

            $tablename = 'addressbook_'.$tablename;
            $table = $xarTables[$tablename];
            $sql = "SELECT nr, name
                    FROM $table
                    ORDER BY nr";
            $result =& $dbconn->Execute($sql);
            if(!$result) return array();

            $arrItems = array();
            for($i=0; !$result->EOF; $result->MoveNext()) {
                list($itemID,$itemName) = $result->fields;
                $arrItems[$i]['id']     = $itemID;
                $arrItems[$i++]['name'] = $itemName;
             }
            $result->Close();

        }
    }

    return $arrItems;

} // END getItems

?>