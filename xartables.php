<?php
/**
 * AddressBook utility functions
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */
/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function addressbook_xartables()
{
    // Initialise table array
    $xarTables = array();
    $prefix = xarDBGetSiteTablePrefix();

    $abAddressTable = $prefix . '_addressbook_address';
    $xarTables['addressbook_address'] = $abAddressTable;

    $abLabelsTable = $prefix . '_addressbook_labels';
    $xarTables['addressbook_labels'] = $abLabelsTable;

    $abCategoriesTable = $prefix . '_addressbook_categories';
    $xarTables['addressbook_categories'] = $abCategoriesTable;

    $abCustomfieldsTable = $prefix . '_addressbook_customfields';
    $xarTables['addressbook_customfields'] = $abCustomfieldsTable;

    $abPrefixesTable = $prefix . '_addressbook_prefixes';
    $xarTables['addressbook_prefixes'] = $abPrefixesTable;

    // Return the table information
    return $xarTables;
}

?>