<?php
/**
 * File: $Id: modifyconfig.php,v 1.3 2003/07/02 02:15:15 garrett Exp $
 *
 * AddressBook utility functions
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

function AddressBook_userapi_input2numeric($args){
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
    return $num; // Zurueckgeben des formatierten Wertes
}

?>