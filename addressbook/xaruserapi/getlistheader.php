<?php
/**
 * File: $Id: getlistheader.php,v 1.2 2003/12/22 07:12:50 garrett Exp $
 *
 * AddressBook userapi getListHeader
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
 * Retrieve the address list results header fields
 *
 * @param int $sort
 * @return array
 */
function addressbook_userapi_getListHeader($args)
{
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
                $returnArray[$i] = array('header'=> strtoupper(_AB_LABEL_NAME));
                break;
            case 'title':
                $returnArray[$i] = array('header'=> strtoupper(_AB_TITLE));
                break;
            case 'sortcompany':
                $returnArray[$i] = array('header'=> strtoupper(_AB_COMPANY));
                break;
            case 'zip':
                $returnArray[$i] = array('header'=> strtoupper(_AB_ZIP));
                break;
            case 'city':
                $returnArray[$i] = array('header'=> strtoupper(_AB_CITY));
                break;
            case 'state':
                $returnArray[$i] = array('header'=> strtoupper(_AB_STATE));
                break;
            case 'country':
                $returnArray[$i] = array('header'=> strtoupper(_AB_COUNTRY));
                break;
        }
        $custom_tab = xarModGetVar(__ADDRESSBOOK__,'custom_tab');
        if ((!empty($custom_tab)) && ($custom_tab != '')) {
            $custUserData = xarModAPIFunc(__ADDRESSBOOK__,'user','getcustfieldinfo',array('flag'=>_AB_CUST_UDCOLANDLABELS)); //gehDEBUG
            foreach($custUserData as $userData) {
                if ($sortCols[$i] == $userData['colName']) {
                    $returnArray[$i] = array('header'=> strtoupper($userData['label']));
                }
            }
        }
    }
    return $returnArray;
} // END getListHeader

?>