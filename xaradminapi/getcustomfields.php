<?php
/**
 * File: $Id: getcustomfields.php,v 1.4 2004/11/13 06:21:57 garrett Exp $
 *
 * AddressBook admin getCustomFields
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
 * getCustomFieldsTypes
 */
function addressbook_adminapi_getCustomfields() 
{
    return xarModAPIFunc(__ADDRESSBOOK__,'user','getcustfieldtypeinfo');

} // END getCustomFields

?>