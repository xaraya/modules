<?php
/**
 * File: $Id: securitycheck.php,v 1.2 2003/12/22 07:12:50 garrett Exp $
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

/**
 * SecurityCheck
 */
function addressbook_userapi_SecurityCheck($value) 
{
    //SecurityCheck
    $value = preg_replace("'<img(.*)src=(.*)(;|\()(.*?)>'i","*******",$value);
    $value = preg_replace("#(<[a-zA-Z])(.*)(;|\()(.*?)(/[a-zA-Z])(.*?)>#si","*******",$value);
    $value = eregi_replace("javascript:","*******",$value);

    return $value;
} // END SecurityCheck

?>