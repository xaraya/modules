<?php
/**
 * AddressBook user getCustomFieldUserInfo
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
 * getCustFieldUserInfo
 */
function addressbook_userapi_getCustFieldUserInfo($args)
{
    extract($args);

    $custFieldUserInfo = array(); // will hold our return data

    if (!empty($custFieldTypeInfo) && !empty($id)) {

        $custData = array (); // will hold the address table data

        $dbconn =& xarDBGetConn();
        $xarTables =& xarDBGetTables();
        $adr_table = $xarTables['addressbook_address'];

        // build a sql statement
        $sql = 'SELECT ';

        foreach($custFieldTypeInfo as $custFieldType) {
            $colName = _AB_CUST_COLPREFIX.$custFieldType['nr'];

            $custData[$colName] = '';
            $sql .= $colName.',';
        }
        $sql = substr($sql,0,-1); // remove trailing comma

        $sql .= ' FROM '.$adr_table.' WHERE nr = '.$id;

        /**
         * Query custom address information
         */
        $result =& $dbconn->Execute($sql);

        if($dbconn->ErrorNo() == 0) {
            if(isset($result)) {
                foreach($custData as $colName=>$colValue) {
                    $custData[$colName] = $result->Fields($colName);
                }
                $result->Close();
            }
        } // end if

        $custFieldUserInfo = $custData;

    } else {
        $errMsg = '';
        if (empty($custFieldTypeInfo)) {
            $errMsg .= 'custFieldTypeInfo not set | ';
        }
        if (empty($id)) {
            $errMsg .= 'id not set';
        }

        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_ERROR,
                    new abUserException("userapi - getCustFieldUserInfo: ".$errMsg)); //gehDEBUG
    }

    return $custFieldUserInfo;

} // END getCustFieldUserInfo

?>
