<?php
/**
 * Object initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage object
 * @author mikespub
 */

/**
 * initialise the object module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function object_init()
{
    xarModSetVar('object', 'itemsperpage', 30);
    xarModSetVar('object', 'SupportShortURLs', 0);

    // Initialisation successful
    return true;
}

/**
 * upgrade the object module from an old version
 * This function can be called multiple times
 */
function object_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0.0':
            // Code to upgrade from version 1.0 goes here

        case '2.0.0':
            // Code to upgrade from version 2.0 goes here

    }
    // Update successful
    return true;
}

/**
 * delete the object module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function object_delete()
{
    xarModDelVar('object', 'itemsperpage');
    xarModDelVar('object', 'SupportShortURLs');

    // Deletion successful
    return true;
}

?>
