<?php
/**
 * File: $Id: input2numeric.php,v 1.2 2003/12/22 07:12:50 garrett Exp $
 *
 * AddressBook utility functions
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
 * Converts numeric form input to Address Book numeric format
 *
 * @param string $inum - numeric to format
 * @return string $num - formated number
 */
function addressbook_utilapi_input2numeric($args)
{

    $num = 0;

    extract($args);
    if( (!isset($inum)) || (empty($inum)) || ($inum=='')) {
        return 'NULL';
    }
    $check_format = ereg_replace(",",".",$inum);
    $split_format = explode(".",$check_format);
    $count_array = count($split_format);

    // example 1000
    if($count_array == 1){
        if(ereg("^[+|-]{0,1}[0-9]{1,}$",$check_format)){
            $num="$split_format[0]";
        }
    }

    // example 1000,20 or 1.000
    if($count_array == 2){
        if(ereg("^[+|-]{0,1}[0-9]{1,}.[0-9]{0,2}$",$check_format)){
            $num="$split_format[0].$split_format[1]";
        }
    }

    // example 1,000.20 or 1.000,20
    if($count_array == 3){
        if(ereg("^[+|-]{0,1}[0-9]{1,}.[0-9]{3}.[0-9]{0,2}$",$check_format)){
            $num="$split_format[0]$split_format[1].$split_format[2]";
        }
    }
    return $num; // Return the formated value
}

?>
