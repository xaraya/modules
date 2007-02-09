<?php
/**
* Highlight initialization functions
*
* @package unassigned
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Initialize the module
*
* This function is only ever called once during the lifetime of a particular
* module instance.
*/
function highlight_init()
{
    // register hooks
    if (!xarModRegisterHook('item', 'transform', 'API', 'highlight', 'user',
        'transform')) {
        return;
    }

    // set module variables
    xarModSetVar('highlight', 'string', 'highlight');
    xarModSetVar('highlight', 'SupportShortURLs', 0);

    // register masks
    xarRegisterMask('AdminHighlight', 'All', 'highlight', 'All', 'All',
        'ACCESS_ADMIN');

    // success
    return true;
}

/**
* Upgrade the module from an old version
*
* This function can be called multiple times.
*
* @param string $oldVersion Version to upgrade from
*/
function highlight_upgrade($oldversion)
{
    // success
    return true;
}

/**
* Delete the module
*
* This function is only ever called once during the lifetime of a particular
* module instance.
*/
function highlight_delete()
{
    // remove hooks
    if (!xarModUnregisterHook('item', 'transform', 'API', 'highlight', 'user',
        'transform')) {
        return;
    }

    // delete module vars and masks
    xarModDelAllVars('highlight');
    xarRemoveMasks('highlight');
    xarRemoveInstances('highlight');

    // success
    return true;
}

?>
