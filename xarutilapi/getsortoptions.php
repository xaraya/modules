<?php
/**
 * File: $Id: getsortoptions.php,v 1.2 2003/12/22 07:12:50 garrett Exp $
 *
 * AddressBook utilapi getSortOptions
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
 * builds an array of sort option values
 *
 * @param none
 * @return array
 */
function addressbook_utilapi_getsortoptions() 
{

    $sortOptions = array();

    $sortOptions[] = array('id'=>'sortname',   'name'=>xarML(_AB_NAME));
    $sortOptions[] = array('id'=>'title',      'name'=>xarML(_AB_TITLE));
    $sortOptions[] = array('id'=>'sortcompany','name'=>xarML(_AB_COMPANY));
    $sortOptions[] = array('id'=>'zip',        'name'=>xarML(_AB_ZIP));
    $sortOptions[] = array('id'=>'city',       'name'=>xarML(_AB_CITY));
    $sortOptions[] = array('id'=>'state',      'name'=>xarML(_AB_STATE));
    $sortOptions[] = array('id'=>'country',    'name'=>xarML(_AB_COUNTRY));

    return $sortOptions;

} // END getSortOptions

?>