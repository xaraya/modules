<?php
/**
 * File: $Id$
 * 
 * Ping initialization functions
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage ping
 * @author John Cox
 */

/**
 * initialise the ping module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function ping_init()
{

    if (!xarModRegisterHook('item', 'update', 'API',
                           'ping', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'create', 'API',
                           'ping', 'admin', 'createhook')) {
        return false;
    } 

    xarRegisterMask('Readping', 'All', 'ping', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('Adminping', 'All', 'ping', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * delete the ping module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function ping_delete()
{

    if (!xarModUnregisterHook('item', 'update', 'API',
                           'ping', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'create', 'API',
                           'ping', 'admin', 'createhook')) {
        return false;
    }

    // Remove Masks and Instances
    xarRemoveMasks('ping');
    xarRemoveInstances('ping'); 

    // Deletion successful
    return true;
} 

/**
 * upgrade the ping module from an old version
 */
function ping_upgrade($oldVersion)
{
    switch($oldVersion) {
    case '1.0.0':
        $modversion['admin']          = 1;
        xarRegisterMask('Adminping', 'All', 'ping', 'Item', 'All:All:All', 'ACCESS_ADMIN');
        if (!xarModRegisterHook('item', 'create', 'API',
                               'ping', 'admin', 'createhook')) {
            return false;
        } 
        break;
    }
    return true;
}

?>