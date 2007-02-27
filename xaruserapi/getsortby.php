<?php
/**
 * AddressBook userapi getSortBy
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
 * getSortBy - retrieves the sorting method from the db
 * @param string $sort - type of sort
 * @return string - contcatenated sort ordering
 */
function addressbook_userapi_getSortBy($args)
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
                $returnArray[$i] = xarML('Name');
                break;
            case 'title':
                $returnArray[$i] = xarML('Title');
                break;
            case 'sortcompany':
                $returnArray[$i] = xarML('Company');
                break;
            case 'zip':
                $returnArray[$i] = xarML('Zip');
                break;
            case 'city':
                $returnArray[$i] = xarML('City');
                break;
            case 'state':
                $returnArray[$i] = xarML('State');
                break;
            case 'country':
                $returnArray[$i] = xarML('Country');
                break;
        }
        $custom_tab = xarModGetVar('addressbook','custom_tab');
        if ((!empty($custom_tab)) && ($custom_tab != '')) {
            $custUserData = xarModAPIFunc('addressbook','user','getcustfieldtypeinfo');
            foreach($custUserData as $userData) {
                if ($sortCols[$i] == $userData['colName']) {
                    $returnArray[$i] = $userData['custLabel'];
                }
            }
        }
    }
    $returnString = $returnArray[0].', '.$returnArray[1];
    return $returnString;
} // END getSortBy

?>
