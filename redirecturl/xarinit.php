<?php
/**
 * Redirect URL
 *
 * @copyright (C) 2003-2005 by Envision Net, Inc.
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 * @link http://www.envisionnet.net
 * @author Brian McGilligan <brian@envisionnet.net>
 *
 * @package Xaraya eXtensible Management System
 * @subpackage Redirect URL module
*/

/**
 * Initialize the module
 */
function redirecturl_init()
{
    // Initialisation successful
    return true;
}

/**
 * Upgrade the module from an old version
 */
function redirecturl_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.0.0':
            // fall through to the next upgrade

        case '1.1.0':
            // Code to upgrade from version 1.1.0 goes here
            break;

        default:
            // Couldn't find a previous version to upgrade
            return;
    }

    // Update successful
    return true;
}

/**
 * Delete the module
 */
function redirecturl_delete()
{
    xarModDelAllVars('redirecturl');

    // Deletion successful
    return true;
}

?>
