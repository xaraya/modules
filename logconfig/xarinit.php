<?php
/**
 * File: $Id: s.xarinit.php 1.17 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 *
 * Dynamic Example initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage logconfig
 * @author Example module development team 
 */

/**
 * initialise the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function logconfig_init()
{
    // this module can't work without the dynamicdata module
    $testmod = xarModIsAvailable('dynamicdata');
    if (!isset($testmod)) return; // some other exception got in our way [locale for instance :)]

    if (!$testmod) {
        $msg = xarML('Please activate the Dynamic Data module first...');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }

    /**
     * import the object definition and properties from some XML file (exported from DD)
     */
    $ids = array();
    $dir = "modules/logconfig/loggers/";
    $itemsnum = 0;
 
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (($file != '.') AND ($file != '..')) {
                    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => "modules/logconfig/loggers/$file"));
                    if (empty($objectid)) return;
                    $ids[] = $objectid;
                    $itemsnum++;
                }
            }
            closedir($dh);
        }
    }

    // save the object ids for later
    //TODO: Review if this is needed
    xarModSetVar('logconfig','objectids',serialize($ids));

    //This is used in admin/view
    xarModSetVar('logconfig','itemstypenumber',$itemsnum);

    xarRegisterMask('AdminLogConfig','All','logconfig','Item','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the example module from an old version
 * This function can be called multiple times
 */
function logconfig_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.5':
        case '1.0.0':
        case '2.0.0':
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
function logconfig_delete()
{

    // delete the dynamic objects and their properties

    $objectids = unserialize(xarModGetVar('logconfig','objectids'));
    foreach ($objectids as $objectid) {
        if (!empty($objectid)) {
            xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
        }
    }
    
    xarModDelVar('logconfig','objectids');
    xarModDelVar('logconfig','itemstypenumber');

    // Remove Masks and Instances
    xarRemoveMasks('logconfig');
    xarRemoveInstances('logconfig');

    // Deletion successful
    return true;
}

?>