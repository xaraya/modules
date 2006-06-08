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

    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                       array('modName' => 'scheduler',
                             'blockType' => 'trigger'))) return;

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
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                               array('modName' => 'scheduler',
                                     'blockType' => 'trigger'))) return;
            // fall through to the next upgrade

        case '1.1.0':
            // fall through to the next upgrade

        case '1.2.0':
            // fall through to the next upgrade

        case '2.0.0':
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

    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type',
                       array('modName' => 'scheduler',
                             'blockType' => 'trigger'))) return;

    // Deletion successful
    return true;
}

?>
