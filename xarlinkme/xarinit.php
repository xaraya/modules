<?php
/**
 * File: $Id: s.xarinit.php 1.17 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage xarLinkMe
 * @based on the phpNuke LinkMe module by Mirko Glotz
 * @author jojodee
 */

/**
 * initialise the xarLinkMe module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xarlinkme_init()
{
    // If Categories API loaded and available, generate proprietary
    // module master category cid and child subcids
    if (xarModIsAvailable('categories')) {
        $xarlinkmecid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name' => 'xarLinkMe',
                'description' => 'xarLinkMe Categories',
                'parent_id' => 0));
        // Note: you can have more than 1 mastercid (cfr. articles module)
        xarModSetVar('xarlinkme', 'number_of_categories', 1);
        xarModSetVar('xarlinkme', 'mastercids', $xarlinkmecid);
        $xarlinkmecategories = array();
        $xarlinkmecategories[] = array('name' => "Promotional",
            'description' => "General promotional banner ads");
        $xarlinkmecategories[] = array('name' => "Web Hosting",
            'description' => "Web hosting related");
        $xarlinkmecategories[] = array('name' => "Business",
            'description' => "Business related banner ads");
        foreach($xarlinkmecategories as $subcat) {
            $xarlinkmesubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' =>
                    $subcat['description'],
                    'parent_id' => $xarlinkmecid));
        }
    }
    // Set up an initial value for a module variable.  Note that all module
    // variables should be initialised with some value in this way rather
    // than just left blank, this helps the user-side code and means that
    // there doesn't need to be a check to see if the variable is set in
    // the rest of the code as it always will be

    xarModSetVar('xarlinkme', 'itemsperpage', 10);
    xarModSetVar('xarlinkme', 'imagedir', 'modules/xarlinkme/xarimages/linkads');
    xarModSetVar('xarlinkme', 'pagetitle', 'Banner and Link Codes');
    xarModSetVar('xarlinkme', 'instructions', 'The following are banners approved for external site linking. Copy the HTML code and paste it into your web page wherever you would like to add the banner link.');
    xarModSetVar('xarlinkme', 'instructions2', 'Choose from one of the banners below, or the Text Link at the end of the banner list.');
    xarModSetVar('xarlinkme', 'txtintro', 'If you would prefer a text link, we suggest the following:');
    xarModSetVar('xarlinkme', 'txtadlead', 'Go...');

     /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */
    xarRegisterMask('ViewxarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadxarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditxarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddxarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeletexarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminxarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the example module from an old version
 * This function can be called multiple times
 */
function xarlinkme_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case 0.1:
        case '0.1.0':
            // Version 0.5 didn't have a 'number' field, it was added
            // in version 1.0
            // Get datbase setup - note that both xarDBGetConn() and xarDBGetTables()
            // return arrays but we handle them differently.  For xarDBGetConn()
            // we currently just want the first item, which is the official
            // database handle.  For xarDBGetTables() we want to keep the entire
            // tables array together for easy reference later on
            // This code could be moved outside of the switch statement if
            // multiple upgrades need it
            list($dbconn) = xarDBGetConn();
            $xartable = xarDBGetTables();
            // It's good practice to name the table and column definitions you
            // are getting - $table and $column don't cut it in more complex
            // modules
            // This code could be moved outside of the switch statement if
            // multiple upgrades need it
            $exampletable = $xartable['example'];
            // Add a column to the table
            // adodb does not provide the functionality to abstract table creates
            // across multiple databases.  Xaraya offers the xarCreateTable function
            // contained in the following file to provide this functionality.
            xarDBLoadTableMaintenanceAPI();
            // $query = "ALTER TABLE $exampletable
            // ADD xar_number INTEGER NOT NULL DEFAULT '0'";
            $query = xarDBAlterTable($exampletable,
                array('command' => 'add',
                    'field' => 'xar_number',
                    'type' => 'integer',
                    'null' => false,
                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
            // At the end of the successful completion of this function we
            // recurse the upgrade to handle any other upgrades that need
            // to be done.  This allows us to upgrade from any version to
            // the current version with ease
            return example_upgrade(1.0);
        case 1.0:
            // Code to upgrade from version 1.0 goes here
            break;
        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xarlinkme_delete()
{
    // Delete any module variables
    xarModDelVar('xarlinkme', 'itemsperpage');
    xarModDelVar('xarlinkme', 'bold');
    if (xarModIsAvailable('categories')) {
        xarModDelVar('xarlinkme', 'number_of_categories');
        xarModDelVar('xarlinkme', 'mastercids');
    }

    xarModDelVar('xarlinkme', 'imagedir', 'modules/xarlinkme/xarimages/linkads');
    xarModDelVar('xarlinkme', 'pagetitle', 'Banner and Link Codes');
    xarModDelVar('xarlinkme', 'instructions', 'Copy the HTML code and paste it into your web page where you would like to add the banner link.');
    xarModDelVar('xarlinkme', 'instructions2', 'Choose from one of the banners below, or the Text Link at the end of the banner list.');
    xarModDelVar('xarlinkme', 'txtintro', 'A text link if you prefer');
    xarModDelVar('xarlinkme', 'txtadlead', 'Go...');

    // Remove Masks and Instances
    // these functions remove all the registered masks and instances of a module
    // from the database. This is not strictly necessary, but it's good housekeeping.
    xarRemoveMasks('xarlinkme');
    xarRemoveInstances('xarlinkme');

    // Deletion successful
    return true;
}

?>
