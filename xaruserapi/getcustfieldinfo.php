<?php
/**
 * AddressBook user getCustomFieldInfo
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Retrieves all custom field information from database
 * takes multiple flags which retrun varying amounts of data
 *
 * _AB_CUST_UDCOLNAMESONLY
 * _AB_CUST_ALLFIELDINFO
 * _AB_CUST_ALLINFO
 *
 * @return array custom field info
 */
function addressbook_userapi_getCustFieldInfo($args)
{
    extract ($args);

    $custFieldInfo = array();

    $custom_tab = xarModGetVar('addressbook','custom_tab');
    if (!empty($custom_tab)) {
        /**
         * Do nothing if the custom_tab is not set in config
         */
        if (empty($flag)) {
            if (empty($id)) {
                $flag = _AB_CUST_ALLFIELDINFO;
            } else {
                $flag = _AB_CUST_ALLINFO;
            }
        } else {
            if (empty($id) && ($flag ==_AB_CUST_ALLINFO)) {
                $flag = _AB_CUST_ALLFIELDINFO;
                xarErrorSet(XAR_USER_EXCEPTION
                           ,_AB_ERR_ERROR
                           ,new abUserException("Invalid Flag: userapi - getCustFieldInfo"));
            }
        }

        $custFieldTypeInfo = xarModAPIFunc('addressbook','user','getcustfieldtypeinfo');

        switch ($flag) {
            case _AB_CUST_UDCOLNAMESONLY: //gehDEBUG - search the code for this / don't think the array index is correctly used / think its fixed now

                $custColNames = array();

                if (!empty($custUserData)) {
                    foreach ($custUserData as $userData) {
                        $custColNames[] = $userData['colName'];
                    } // END foreach

                } else {
                    /**
                     * No data passed so I guess we want the current names
                     */
                    foreach ($custFieldTypeInfo as $custFieldTypeData) {
                        $custColNames[] = $custFieldTypeData['colName'];
                    } // END foreach
                }

                $custFieldInfo = $custColNames;
                break;

            case _AB_CUST_ALLFIELDINFO:
                $custFieldInfo = $custFieldTypeInfo;
                break;

            case _AB_CUST_ALLINFO:
            default:

                /*
                 * This copies all the base info into our return var. we just need to
                 * update it the user data from the address table
                 */
                $custFieldInfo = $custFieldTypeInfo;

                $custFieldUserInfo = xarModAPIFunc('addressbook','user'
                                                  ,'getcustfielduserinfo'
                                                  ,array('custFieldTypeInfo'=>$custFieldTypeInfo,'id'=>$id));

                /**
                 * Build a single object with custom type & data info
                 */
                foreach ($custFieldInfo as $rowIdx=>$custFieldInfoRow) {
                    /*
                     * Copy the new data to our return var
                     */

                    // shorthand use of $colName
                    $colName = $custFieldInfoRow['colName'];

                    /**
                     * Determine each data type & format accordingly
                     */
                    if (isset($custFieldUserInfo[$colName])) { // leave off the trailing ')' in IF and the Xaraya exception handler does not report syntax error //gehDEBUG
                        $custFieldInfo[$rowIdx]['userData'] = $custFieldUserInfo[$colName];
                    } elseif (strstr($custFieldInfo[$rowIdx]['custType'],_AB_CUSTOM_BLANKLINE)) {
                        $custFieldInfo[$rowIdx]['userData'] = _AB_HTML_LINEBREAK;
                        $custFieldInfo[$rowIdx]['colName'] = '';
                    } elseif (strstr($custFieldInfo[$rowIdx]['custType'],_AB_CUSTOM_HORIZ_RULE)) {
                        $custFieldInfo[$rowIdx]['userData'] = _AB_HTML_HORIZRULE;
                        $custFieldInfo[$rowIdx]['colName'] = '';
                    }

                } // END foreach

                break;

        } // END switch
    } // END if custom_tab

    return $custFieldInfo;

} // END getCustomFieldInfo
?>
