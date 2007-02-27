<?php
/**
 * AddressBook admin getCustomFields
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
 * getCustomFieldsTypes
 */
function addressbook_adminapi_getCustomfields()
{
    return xarModAPIFunc('addressbook','user','getcustfieldtypeinfo');

} // END getCustomFields

?>
