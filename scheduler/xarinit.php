<?php
/**
 * File: $Id$
 *
 * Scheduler initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage scheduler
 * @author mikespub
 */

/**
 * initialise the scheduler module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function scheduler_init()
{
    xarModSetVar('scheduler', 'trigger', 'disabled');
    xarModSetVar('scheduler', 'lastrun', 0);

    xarRegisterMask('AdminScheduler', 'All', 'scheduler', 'All', 'All', 'ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the scheduler module from an old version
 * This function can be called multiple times
 */
function scheduler_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
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
 * delete the scheduler module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function scheduler_delete()
{
    xarModDelVar('scheduler', 'trigger');
    xarModDelVar('scheduler', 'lastrun');
    xarModDelVar('scheduler', 'jobs');

    xarRemoveMasks('scheduler');

    // Deletion successful
    return true;
}

?>
