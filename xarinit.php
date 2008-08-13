<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * initialise the scheduler module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function scheduler_init()
{
    xarModVars::set('scheduler', 'trigger', 'disabled');
    xarModVars::set('scheduler', 'lastrun', 0);

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
    xarModVars::delete('scheduler', 'trigger');
    xarModVars::delete('scheduler', 'lastrun');
    xarModVars::delete('scheduler', 'jobs');

    xarRemoveMasks('scheduler');

    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type',
                       array('modName' => 'scheduler',
                             'blockType' => 'trigger'))) return;

    // Deletion successful
    return true;
}

?>
