<?php
/**
 * File: $Id: s.xarinit.php 1.17 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 *
 * icecast initialization function
 *
 * @copyright (C) 2004 by Johnny Robeson
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link
 *
 * @subpackage icecast
 * @author Johnny Robeson
 */


/**
 * Initialise the icecast module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 *
 * @return bool
 */
function icecast_init()
{

    // will activate later if necessary
    /*if (!extension_loaded('curl')) {
        $msg=xarML('Your PHP configuration does not seem to include the required cURL extension. Please refer to http://www.php.net/manual/en/ref.curl.php on how to install it.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
                        new SystemException($msg));
        return;
    }*/
    xarModSetVar('icecast', 'DefaultServer', 'localhost');
    xarModSetVar('icecast', 'DefaultPort', 8000);
    //xarModSetVar('icecast', 'itemsperpage', 10);
    // If your module supports short URLs, the website administrator should
    // be able to turn it on or off in your module administration
    //xarModSetVar('icecast', 'SupportShortURLs', 0);
    // Register Block types (this *should* happen at activation/deactivation)
    //if (!xarModAPIFunc('blocks',
    //        'admin',
    //        'register_block_type',
    //        array('modName' => 'icecast',
    //            'blockType' => 'others'))) return;


    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    //xarRegisterMask('ReadicecastBlock', 'All', 'icecast', 'Block', 'All', 'ACCESS_OVERVIEW');
    //xarRegisterMask('Viewicecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    //xarRegisterMask('Readicecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_READ');
    //xarRegisterMask('Editicecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_EDIT');
    //xarRegisterMask('Addicecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_ADD');
    //xarRegisterMask('Deleteicecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminIcecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * Upgrade the icecast module from an old version
 *
 * This function can be called multiple times
 *
 * @param string oldVersion
 * @return bool
 */
function icecast_upgrade($oldVersion)
{
    // Upgrade dependent on old version number
    switch ($oldVersion) {

        case '1.0.0':
            // Code to upgrade from version 1.0 goes here
            break;
        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * Delete the icecast module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 *
 * @return bool
 */
function icecast_delete()
{

    xarModDelVar('icecast', 'DefaultServer');
    xarModDelVar('icecast', 'DefaultPort');


    //xarModDelVar('icecast', 'SupportShortURLs');
    // UnRegister blocks
    //if (!xarModAPIFunc('blocks',
    //        'admin',
    //        'unregister_block_type',
    //        array('modName' => 'icecast',
    //            'blockType' => 'first'))) return;

    xarRemoveMasks('icecast');


    // Deletion successful
    return true;
}

?>
