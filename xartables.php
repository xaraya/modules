<?php
/**
 * DOSSIER utility functions
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @author Chad Kraeft <cdavidkraeft@miragelab.com>
 */
/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function dossier_xartables()
{
    // Initialise table array
    $xarTables = array();
    $prefix = xarDBGetSiteTablePrefix();

    $contacts_Table = $prefix . '_dossier_contacts';
    $xarTables['dossier_contacts'] = $contacts_Table;

    $locations_Table = $prefix . '_dossier_locations';
    $xarTables['dossier_locations'] = $locations_Table;

    $locationdata_Table = $prefix . '_dossier_locationdata';
    $xarTables['dossier_locationdata'] = $locationdata_Table;

    $logs_Table = $prefix . '_dossier_logs';
    $xarTables['dossier_logs'] = $logs_Table;

    $reminders_Table = $prefix . '_dossier_reminders';
    $xarTables['dossier_reminders'] = $reminders_Table;

    $addressbook_links_Table = $prefix . '_dossier_addressbook_links';
    $xarTables['dossier_addressbook_links'] = $addressbook_links_Table;

    $friendslist_Table = $prefix . '_dossier_friendslist';
    $xarTables['dossier_friendslist'] = $friendslist_Table;

    $relationships_Table = $prefix . '_dossier_relationships';
    $xarTables['dossier_relationships'] = $relationships_Table;

    // Return the table information
    return $xarTables;
}

?>
