<?php
/**
 * File: $Id: getsortby.php,v 1.2 2003/12/22 07:12:50 garrett Exp $
 *
 * AddressBook userapi getSortBy
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
 * getSortBy - retrieves the sorting method from the db
 * @param string $sort - type of sort
 * @return string - contcatenated sort ordering
 */
function addressbook_userapi_getSortBy($args){
    extract($args);
    if (!isset($sort)) {
        return false;
    }
    if ($sort == 1) {
        $sortCols = explode(',',xarModGetVar(__ADDRESSBOOK__, 'sortorder_1'));
    }
    else {
        $sortCols = explode(',',xarModGetVar(__ADDRESSBOOK__, 'sortorder_2'));
    }
    for ($i=0;$i<2;$i++) {
        switch ($sortCols[$i]) {
            case 'sortname':
                $returnArray[$i] = _AB_NAME;
                break;
            case 'title':
                $returnArray[$i] = _AB_TITLE;
                break;
            case 'sortcompany':
                $returnArray[$i] = _AB_COMPANY;
                break;
            case 'zip':
                $returnArray[$i] = _AB_ZIP;
                break;
            case 'city':
                $returnArray[$i] = _AB_CITY;
                break;
            case 'state':
                $returnArray[$i] = _AB_STATE;
                break;
            case 'country':
                $returnArray[$i] = _AB_COUNTRY;
                break;
        }
        $custom_tab = xarModGetVar(__ADDRESSBOOK__,'custom_tab');
        if ((!empty($custom_tab)) && ($custom_tab != '')) {
            $custUserData = xarModAPIFunc(__ADDRESSBOOK__,'user','getcustfieldinfo',array('flag'=>_AB_CUST_UDCOLANDLABELS)); //gehDEBUG
            foreach($custUserData as $userData) {
                if ($sortCols[$i] == $userData['colName']) {
                    $returnArray[$i] = $userData['label'];
                }
            }
        }
    }
    $returnString = $returnArray[0].', '.$returnArray[1];
    return $returnString;
} // END getSortBy

?>