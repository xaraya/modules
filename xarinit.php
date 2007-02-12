<?php
/**
 * Search System - Present searches via hooks
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search Module
 * @link http://xaraya.com/index.php/release/32.html
 * @author Search Module Development Team
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
    xarModSetVar('search', 'showsearches', true);
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
    xarRegisterMask('AdminSearch', 'All', 'search', 'All', 'All', 'ACCESS_ADMIN');
    return true;
}

/**
 * Upgrade the search module from an old version
 *
 * @author Johnny Robeson
 * @author Jo Dalle NOgare
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

    //fall through to next version upgrade
    case '0.2.0':
        //register AdminSearch mask
        xarRegisterMask('AdminSearch', 'All', 'search', 'All', 'All', 'ACCESS_ADMIN');
        //admin configurable prior search display
         xarModSetVar('search', 'showsearches', true);
    //current version
    case '0.3.0':
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
    xarModDelAllVars('search');
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