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
    // If your module supports short URLs, the website administrator should
    // be able to turn it on or off in your module administration
    //xarModSetVar('icecast', 'SupportShortURLs', 0);
    
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                        array('modName' => 'icecast',
                             'blockType' => 'nowplaying'))) return;

    xarRegisterMask('ReadIcecastBlock', 'All', 'icecast', 'Block', 'All', 'ACCESS_OVERVIEW');
    //xarRegisterMask('Viewicecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    //xarRegisterMask('Readicecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_READ');
    //xarRegisterMask('Editicecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_EDIT');
    //xarRegisterMask('Addicecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_ADD');
    //xarRegisterMask('Deleteicecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminIcecast', 'All', 'icecast', 'Item', 'All:All:All', 'ACCESS_ADMIN');
 
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

        
    }

    return true;
}

/**
 * Delete the icecast module
 *
 * @return bool
 */
function icecast_delete()
{

    xarModDelVar('icecast', 'DefaultServer');
    xarModDelVar('icecast', 'DefaultPort');


    //xarModDelVar('icecast', 'SupportShortURLs');
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                        array('modName' => 'icecast',
                    'blockType' => 'nowplaying'))) return;

    xarRemoveMasks('icecast');


    // Deletion successful
    return true;
}

?>
