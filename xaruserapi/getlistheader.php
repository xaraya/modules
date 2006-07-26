<?php
/**
 * AddressBook userapi getListHeader
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
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
        $sortCols = explode(',',xarModGetVar('addressbook', 'sortorder_1'));
    }
    else {
        $sortCols = explode(',',xarModGetVar('addressbook', 'sortorder_2'));
    }
    for ($i=0;$i<2;$i++) {
        switch ($sortCols[$i]) {
            case 'sortname':
                $returnArray[$i] = array('header'=> strtoupper(xarML('NAME')));
                break;
            case 'title':
                $returnArray[$i] = array('header'=> strtoupper(xarML('Title')));
                break;
            case 'sortcompany':
                $returnArray[$i] = array('header'=> strtoupper(xarML('Company')));
                break;
            case 'zip':
                $returnArray[$i] = array('header'=> strtoupper(xarML('Zip')));
                break;
            case 'city':
                $returnArray[$i] = array('header'=> strtoupper(xarML('City')));
                break;
            case 'state':
                $returnArray[$i] = array('header'=> strtoupper(xarML('State')));
                break;
            case 'country':
                $returnArray[$i] = array('header'=> strtoupper(xarML('Country')));
                break;
            default:
                // do nothing
                break;
        }
        $custom_tab = xarModGetVar('addressbook','custom_tab');
        if ((!empty($custom_tab)) && ($custom_tab != '')) {
            $custUserData = xarModAPIFunc('addressbook','user','getcustfieldtypeinfo');
            foreach($custUserData as $userData) {
                if ($sortCols[$i] == $userData['colName']) {
                    $returnArray[$i] = array('header'=> strtoupper($userData['custLabel']));
                }
            }
        }
    }

    /**
     * Check if any of the custom fields are selected for display on the search results
     */
    $custom_tab = xarModGetVar('addressbook','custom_tab');
    if ((!empty($custom_tab)) && ($custom_tab != '')) {
        $custUserData = xarModAPIFunc('addressbook','user','getcustfieldtypeinfo');
        foreach($custUserData as $userData) {
            if ($userData['custDisplay']) {
                if (!empty($userData['custShortLabel'])) {
                    $returnArray[] = array('header'=> '<acronym title="'.$userData['custLabel'].'">'.strtoupper($userData['custShortLabel'].'</acronym>'));
                } else {
                    $returnArray[] = array('header'=> strtoupper($userData['custLabel']));
                }
            }
        }
    }

    return $returnArray;
} // END getListHeader

?>
