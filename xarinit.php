<?php
/**
 * Initialise the encyclopedia module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

/**
 * Initialise the Encyclopedia module
 *
 * @access public
 * @param none $
 * @returns bool
 * @raise DATABASE_ERROR
 */
function encyclopedia_init()
{
// Create tables the good ole way
/*    $q = new xarQuery();
    $prefix = xarDBGetSiteTablePrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_encyclopedia";
    if (!$q->run($query)) return;

    $query = "CREATE TABLE " . $prefix . "_encyclopedia (
            xar_id int(10) NOT NULL auto_increment,
            xar_vid int(10) NOT NULL default '0',
            xar_term varchar(60) NOT NULL default '',
            xar_pronunciation varchar(100) NOT NULL default '',
            xar_related mediumtext NOT NULL,
            xar_links text NOT NULL,
            xar_definition text NOT NULL,
            xar_author varchar(200) NOT NULL default '',
            xar_image varchar(60) NOT NULL default '',
            xar_validated int(10) NOT NULL default '1',
            xar_active int(10) NOT NULL default '1',
            xar_date datetime NOT NULL default '0000-00-00 00:00:00',
            PRIMARY KEY(xar_id)) TYPE=MyISAM";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_encyclopedia_volumes";
    if (!$q->run($query)) return;

    $query = "CREATE TABLE " . $prefix . "_encyclopedia_volumes (
            xar_vid int(11) NOT NULL auto_increment,
            xar_volume VARCHAR(250) NOT NULL,
            xar_description text NOT NULL,
            PRIMARY KEY(xar_vid))";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_encyclopedia_volumes
            VALUES ('0','Volume Example','This has to be here for a stupid reason. Just delete it.')";
    if (!$q->run($query)) return;

*/


/*-----------------------------------
* Create a DD object to hold the data
*/
    // Where is the XML file that defines this object?
    $path = "modules/encyclopedia/xardata/";

    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'encyclopedia.xml'));
    if (empty($objectid)) return;

    // save the id for later
    xarModSetVar('encyclopedia','encyclopediaid',$objectid);


/*-----------------------------------
* Create a parent category for the volumes, and a default volume
*/
    $cid = xarModAPIFunc('categories', 'admin', 'create',
                         array('name' => 'Volumes',
                               'description' => 'Encyclopedia Volumes',
                               'parent_id' => 0));
    // save the id for later
    xarModSetVar('encyclopedia', 'volumes', $cid);

    $subcid = xarModAPIFunc('categories', 'admin', 'create',
                            array('name' => "Volume 1",
                                'description' => "Default Volume",
                                'parent_id' => $cid));

/*-----------------------------------
* Enable hooks for this module
*/
    // From categories module
    xarModAPIFunc('modules','admin','enablehooks',
          array('callerModName' => 'encyclopedia', 'hookModName' => 'categories'));

    // From and to search module
    xarModAPIFunc('modules','admin','enablehooks',
          array('callerModName' => 'encyclopedia', 'hookModName' => 'search'));
    xarModAPIFunc('modules','admin','enablehooks',
          array('callerModName' => 'search', 'hookModName' => 'encyclopedia'));

    // From comments module
    xarModAPIFunc('modules','admin','enablehooks',
          array('callerModName' => 'encyclopedia', 'hookModName' => 'comments'));

/*-----------------------------------
* Create module variables for the configuration
*/
    xarModSetVar('encyclopedia', 'welcome', 'Enter your welcome message here');
    xarModSetVar('encyclopedia', 'upload', 1);
    xarModSetVar('encyclopedia', 'imagewidth', 350);
    xarModSetVar('encyclopedia', 'imageheight', 350);
    xarModSetVar('encyclopedia', 'autolinks', 1);
    xarModSetVar('encyclopedia', 'columns', 3);
    xarModSetVar('encyclopedia', 'longdisplay', 1);
    xarModSetVar('encyclopedia', 'layout', 1);
    xarModSetVar('encyclopedia', 'itemsperpage', 10);
    xarModSetVar('encyclopedia', 'allowsearch', 1);
    xarModSetVar('encyclopedia', 'allowletters', 1);

/*-----------------------------------
* Create masks for security checks we want in the code
*/
    xarRegisterMask('ViewEncyclopedia','All','encyclopedia','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadEncyclopedia','All','encyclopedia','All','All','ACCESS_READ');
    xarRegisterMask('CommentEncyclopedia','All','encyclopedia','All','All','ACCESS_COMMENT');
    xarRegisterMask('EditEncyclopedia','All','encyclopedia','All','All','ACCESS_EDIT');
    xarRegisterMask('DeleteEncyclopedia','All','encyclopedia','All','All','ACCESS_DELETE');
    xarRegisterMask('AddEncyclopedia','All','encyclopedia','All','All','ACCESS_ADD');
    xarRegisterMask('AdminEncyclopedia','All','encyclopedia','All','All','ACCESS_ADMIN');

/*-----------------------------------
* Initialisation was successful
*/
    return true;
}

function encyclopedia_activate()
{
    // Nothing to do here
    return true;
}

/**
 * Upgrade the encyclopedia module from an old version
 *
 * @access public
 * @param oldVersion $
 * @returns bool
 * @raise DATABASE_ERROR
 */
function encyclopedia_upgrade($oldVersion)
{
    // Upgrade dependent on old version number
    switch ($oldVersion) {
        case 1.01:
            break;
        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * Delete the encyclopedia module
 *
 * @access public
 * @param none
 * @returns bool
 */
function encyclopedia_delete()
{
/*
// Remove the table with the data
    $q = new xarQuery();
    $prefix = xarDBGetSiteTablePrefix() . '_encyclopedia';
    $tables = xarDBGetTables();
    foreach ($tables as $table) {
        if (strpos($table,$prefix) === 0) {
            $query = "DROP TABLE IF EXISTS " . $table;
            if (!$q->run($query)) return;
        }
    }
*/

/*-----------------------------------
* Remove the object with the data
*/
    $objectid = xarModGetVar('encyclopedia','encyclopediaid');
    if (!empty($objectid)) {
        $object = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                array('objectid' => $objectid));

        // Remove the objects properties and data
        if (isset($object)) {
            $properties = xarModAPIFunc('dynamicdata','user','getprop',
                                           array('modid' => $object['moduleid'],
                                                 'itemtype' => $object['itemtype'],
                                                 'allprops' => true));
            foreach ($properties as $property)
                xarModAPIFunc('dynamicdata','admin','deleteprop',array('prop_id' => $property['id']));
        }
        // Remove the object definition
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

/*-----------------------------------
* Remove the hooks to and from this module
*/
    xarModAPIFunc('modules','admin','disablehooks',
          array('callerModName' => 'encyclopedia', 'hookModName' => 'categories'));
    xarModAPIFunc('modules','admin','enablehooks',
          array('callerModName' => 'encyclopedia', 'hookModName' => 'search'));
    xarModAPIFunc('modules','admin','enablehooks',
          array('callerModName' => 'search', 'hookModName' => 'encyclopedia'));
    xarModAPIFunc('modules','admin','enablehooks',
          array('callerModName' => 'encyclopedia', 'hookModName' => 'comments'));

/*-----------------------------------
* Clean up all the other database entries we created
*/
    $cid = xarModGetVar('encyclopedia', 'volumes');
    $result = xarModAPIFunc('categories','admin','deletecat', array('cid' => $cid));
    xarModDelAllVars('encyclopedia');
    xarRemoveMasks('encyclopedia');

/*-----------------------------------
* Delete was successful
*/
    return true;
}

?>