<?php
/**
 * AddressBook utilapi num4display
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
 * Format of the number to display
 *
 * @param string $inum
 * @return string - number formatted for display
 */
function addressbook_userapi_num4display($args)
{
    extract($args);
    if( (!isset($inum)) || (empty($inum)) || ($inum=='')) {
        return '';
    }
    $returnValue = '';
    $dateformat = xarModGetVar('addressbook','numformat');
    if ($dateformat == '9.999,99') {
        $returnValue = number_format($inum,2,',','.');
    }
    else {
        $returnValue = number_format($inum,2,'.',',');
    }
    return $returnValue;
}

?>
