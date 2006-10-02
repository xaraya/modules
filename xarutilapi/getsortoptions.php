<?php
/**
 * AddressBook utilapi getSortOptions
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
 * builds an array of sort option values
 *
 * @param none
 * @return array
 */
function addressbook_utilapi_getsortoptions()
{

    $sortOptions = array();

    $sortOptions[] = array('id'=>'sortname',   'name'=>xarML('Name'));
    $sortOptions[] = array('id'=>'title',      'name'=>xarML('Title'));
    $sortOptions[] = array('id'=>'sortcompany','name'=>xarML('Company'));
    $sortOptions[] = array('id'=>'zip',        'name'=>xarML('Zip'));
    $sortOptions[] = array('id'=>'city',       'name'=>xarML('City'));
    $sortOptions[] = array('id'=>'state',      'name'=>xarML('State'));
    $sortOptions[] = array('id'=>'country',    'name'=>xarML('Country'));

    return $sortOptions;

} // END getSortOptions

?>
