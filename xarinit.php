<?php
/**
 * Ajax Library - A Prototype library collection.
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license   New BSD License
 * @link http://www.xaraya.com
 *
 * @subpackage Ajax Library Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */

/**
 *    Initialize the module
 */
function ajax_init()
{
    xarTplRegisterTag(
        'ajax', 'ajax-lib', array(),
        'ajax_libapi_handleincludes'
    );
    // Initialisation successful
    return true;
}


/**
 * Upgrade the module from an old version
 * @return bool
 */
function ajax_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.9.0':

            break;

        default:
            // Couldn't find a previous version to upgrade
            return false;
    }

    // Update successful
    return true;
}

/**
 * Delete the module
 */
function ajax_delete()
{
    // Deletion successful
    return true;
}
?>
