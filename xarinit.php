<?php
/**
* Files initialization functions
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Initialize the module
*
* This function is only ever called once during the lifetime of a particular
* module instance.
*/
function files_init()
{
    // register module vars
    xarModSetVar('Files', 'SupportShortURLs', 0);
    xarModSetVar('audio', 'archive_dir', xarPreCoreGetVarDirPath().'/files');

    // register masks
    xarRegisterMask('ViewFiles',   'All', 'Files', 'Item', '', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadFiles',   'All', 'Files', 'Item', '', 'ACCESS_READ');
    xarRegisterMask('EditFiles',   'All', 'Files', 'Item', '', 'ACCESS_EDIT');
    xarRegisterMask('AddFiles',    'All', 'Files', 'Item', '', 'ACCESS_ADD');
    xarRegisterMask('DeleteFiles', 'All', 'Files', 'Item', '', 'ACCESS_DELETE');
    xarRegisterMask('AdminFiles',  'All', 'Files', 'Item', '', 'ACCESS_ADMIN');

    return true;
}

/**
* Upgrade the module from an old version
*
* This function can be called multiple times.
*
* @param string $oldVersion Version to upgrade from
*/
function files_upgrade($oldversion)
{
    return true;
}

/**
* Delete the module
*
* This function is only ever called once during the lifetime of a particular
* module instance.
*/
function files_delete()
{
    // remove module vars and masks
    xarModDelAllVars('files');
    xarRemoveMasks('files');

    // Deletion successful
    return true;
}

?>
