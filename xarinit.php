<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 *
 * Search System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage search module
 * @author Johnny Robeson
 */
/**
 * Initialise the search module
 *
 * @author Johnny Robeson
 * @access public
 * @param none $
 * @return true on success or void or false on failure
 * @throws 'DATABASE_ERROR'
 * @todo nothing
 */
function search_init()
{
    xarModSetVar('search', 'resultsperpage', 10);
    // Register blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'search',
                'blockType' => 'search'))) return;

    // Register search hook
    xarModRegisterHook('item','search','GUI','search','user','searchform');

    // Register Mask
    xarRegisterMask('ReadSearch', 'All', 'search', 'All', 'All', 'ACCESS_READ');

    return true;
}

/**
 * Upgrade the search module from an old version
 *
 * @author Johnny Robeson
 * @access public
 * @param  $oldVersion
 * @return true on success or false on failure
 * @throws no exceptions
 * @todo nothing
 */
function search_upgrade($oldversion)
{
    switch($oldversion) {
    case '0.1':
        // Register search hook
        xarModRegisterHook('item','search','GUI','search','user','searchform');
        break;
    }
    return true;
}
/**
 * Delete the search module
 *
 * @author Johnny Robeson
 * @access public
 * @param no $ parameters
 * @return true on success or false on failure
 * @todo restore the default behaviour prior to 1.0 release
 */
function search_delete()
{
    xarModDelVar('search', 'resultsperpage');
    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'search',
                'blockType' => 'search'))) return;
    // Remove Masks and Instances
    xarRemoveMasks('search');
    xarRemoveInstances('search');

    // Unregister search hook
    xarModUnRegisterHook('item','search','GUI','search','user','searchform');

    return true;
}

?>