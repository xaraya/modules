<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Marie Altobelli (Ladyofdragons)
// Current Maintainer: Michael Cortez (mcortez)
// Purpose of file:  Initialisation functions for uploads
// ----------------------------------------------------------------------

/**
 * initialise the module
 */
function uploads_init()
{
    //Not needed anymore with the dependency checks.
    if (!xarMod::isAvailable('mime')) {
        $msg = xarML('The mime module should be activated first');
        throw new Exception($msg);
    }

    // load the predefined constants
    xarMod::apiLoad('uploads', 'user');

    if (xarServer::getVar('SCRIPT_FILENAME')) {
        $base_directory = dirname(realpath(xarServer::getVar('SCRIPT_FILENAME')));
    } else {
        $base_directory = './';
    }
    xarModVars::set('uploads', 'uploads_directory', 'Change me to something outside the webroot');
    xarModVars::set('uploads', 'imports_directory', 'Change me to something outside the webroot');
    xarModVars::set('uploads', 'file.maxsize', '10000000');
    xarModVars::set('uploads', 'file.delete-confirmation', true);
    xarModVars::set('uploads', 'file.auto-purge', false);
    xarModVars::set('uploads', 'file.obfuscate-on-import', false);
    xarModVars::set('uploads', 'file.obfuscate-on-upload', true);
    xarModVars::set('uploads', 'path.imports-cwd', xarModVars::get('uploads', 'imports_directory'));
    xarModVars::set('uploads', 'dd.fileupload.stored', true);
    xarModVars::set('uploads', 'dd.fileupload.external', true);
    xarModVars::set('uploads', 'dd.fileupload.upload', true);
    xarModVars::set('uploads', 'dd.fileupload.trusted', true);
    xarModVars::set('uploads', 'file.auto-approve', _UPLOADS_APPROVE_ADMIN);

    $data['filters']['inverse']                     = false;
    $data['filters']['mimetypes'][0]['typeId']      = 0;
    $data['filters']['mimetypes'][0]['typeName']    = xarML('All');
    $data['filters']['subtypes'][0]['subtypeId']    = 0;
    $data['filters']['subtypes'][0]['subtypeName']  = xarML('All');
    $data['filters']['status'][0]['statusId']       = 0;
    $data['filters']['status'][0]['statusName']     = xarML('All');
    $data['filters']['status'][_UPLOADS_STATUS_SUBMITTED]['statusId']    = _UPLOADS_STATUS_SUBMITTED;
    $data['filters']['status'][_UPLOADS_STATUS_SUBMITTED]['statusName']  = 'Submitted';
    $data['filters']['status'][_UPLOADS_STATUS_APPROVED]['statusId']     = _UPLOADS_STATUS_APPROVED;
    $data['filters']['status'][_UPLOADS_STATUS_APPROVED]['statusName']   = 'Approved';
    $data['filters']['status'][_UPLOADS_STATUS_REJECTED]['statusId']     = _UPLOADS_STATUS_REJECTED;
    $data['filters']['status'][_UPLOADS_STATUS_REJECTED]['statusName']   = 'Rejected';
    $filter['fileType']     = '%';
    $filter['fileStatus']   = '';

    $mimetypes =& $data['filters']['mimetypes'];
    $mimetypes += xarMod::apiFunc('mime', 'user', 'getall_types');

    xarModVars::set('uploads', 'view.filter', serialize(['data' => $data,'filter' => $filter]));
    unset($mimetypes);

    xarModVars::set('uploads', 'items_per_page', 200);
    xarModVars::set('uploads', 'file.cache-expire', 0);
    xarModVars::set('uploads', 'file.allow-duplicate-upload', 0);

    // Get datbase setup
    $dbconn = xarDB::getConn();

    $xartable = xarDB::getTables();

    $file_entry_table = $xartable['file_entry'];
    $file_data_table  = $xartable['file_data'];
    $file_assoc_table = $xartable['file_associations'];

    sys::import('xaraya.tableddl');

    $file_entry_fields = [
        'xar_fileEntry_id' => ['type'=>'integer', 'size'=>'big', 'null'=>false,  'increment'=>true,'primary_key'=>true],
        'xar_user_id'      => ['type'=>'integer', 'size'=>'big', 'null'=>false],
        'xar_filename'     => ['type'=>'varchar', 'size'=>128,   'null'=>false],
        'xar_location'     => ['type'=>'varchar', 'size'=>255,   'null'=>false],
        'xar_status'       => ['type'=>'integer', 'size'=>'tiny','null'=>false,  'default'=>'0'],
        'xar_filesize'     => ['type'=>'integer', 'size'=>'big',    'null'=>false],
        'xar_store_type'   => ['type'=>'integer', 'size'=>'tiny',     'null'=>false],
        'xar_mime_type'    => ['type'=>'varchar', 'size' =>128,  'null'=>false,  'default' => 'application/octet-stream'],
        'xar_extrainfo'    => ['type'=>'text'],
    ];


    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $sql is empty
    $query   =  xarTableDDL::createTable($file_entry_table, $file_entry_fields);
    $result  =& $dbconn->Execute($query);

    $file_data_fields = [
        'xar_fileData_id'  => ['type'=>'integer','size'=>'big','null'=>false,'increment'=>true, 'primary_key'=>true],
        'xar_fileEntry_id' => ['type'=>'integer','size'=>'big','null'=>false],
        'xar_fileData'     => ['type'=>'blob','size'=>'medium','null'=>false],
    ];

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $sql is empty
    $query  =  xarTableDDL::createTable($file_data_table, $file_data_fields);
    $result =& $dbconn->Execute($query);

    $file_assoc_fields = [
        'xar_fileEntry_id' => ['type'=>'integer', 'size'=>'big', 'null'=>false],
        'xar_modid'        => ['type'=>'integer', 'size'=>'big', 'null'=>false],
        'xar_itemtype'     => ['type'=>'integer', 'size'=>'big', 'null'=>false, 'default'=>'0'],
        'xar_objectid'       => ['type'=>'integer', 'size'=>'big', 'null'=>false, 'default'=>'0'],
    ];


    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $sql is empty
    $query   =  xarTableDDL::createTable($file_assoc_table, $file_assoc_fields);
    $result  =& $dbconn->Execute($query);

    $instances[0]['header'] = 'external';
    $instances[0]['query']  = xarController::URL('uploads', 'admin', 'privileges');
    $instances[0]['limit']  = 0;

    xarPrivileges::defineInstance('uploads', 'File', $instances);

    xarMasks::register('ViewUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_OVERVIEW');
    xarMasks::register('ReadUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_READ');
    xarMasks::register('EditUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_EDIT');
    xarMasks::register('AddUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_ADD');
    xarMasks::register('ManageUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_DELETE');
    xarMasks::register('AdminUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_ADMIN');

    xarPrivileges::register('ViewUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_OVERVIEW');
    xarPrivileges::register('ReadUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_READ');
    xarPrivileges::register('EditUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_EDIT');
    xarPrivileges::register('AddUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_ADD');
    xarPrivileges::register('ManageUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_DELETE');
    xarPrivileges::register('AdminUploads', 'All', 'uploads', 'File', 'All', 'ACCESS_ADMIN');

    /**
     * Register hooks
     */
    if (!xarModHooks::register('item', 'transform', 'API', 'uploads', 'user', 'transformhook')) {
        $msg = xarML('Could not register hook');
        throw new Exception($msg);
    }
    /*
        if (!xarModHooks::register('item', 'create', 'API', 'uploads', 'admin', 'createhook')) {
             $msg = xarML('Could not register hook');
            throw new Exception($msg);
        }
        if (!xarModHooks::register('item', 'update', 'API', 'uploads', 'admin', 'updatehook')) {
             $msg = xarML('Could not register hook');
            throw new Exception($msg);
        }
        if (!xarModHooks::register('item', 'delete', 'API', 'uploads', 'admin', 'deletehook')) {
             $msg = xarML('Could not register hook');
            throw new Exception($msg);
        }
        // when a whole module is removed, e.g. via the modules admin screen
        // (set object ID to the module name !)
        if (!xarModHooks::register('module', 'remove', 'API', 'uploads', 'admin', 'removehook')) {
             $msg = xarML('Could not register hook');
            throw new Exception($msg);
        }

        if (xarCurrentErrorType() !== XAR_NO_EXCEPTION) {
            // if there was an error, make sure to remove the tables
            // so the user can try the install again
            uploads_delete();
            return;
        }
    */

    return true;
}

/**
 * upgrade the uploads module from an old version
 */
function uploads_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.1.0':

        default:
            return true;
    }

    return true;
}

/**
 * delete the uploads module
 */
function uploads_delete()
{
    xarModHooks::unregister('item', 'transform', 'API', 'uploads', 'user', 'transformhook');
    /*
        xarModHooks::unregister('item', 'create', 'API', 'uploads', 'admin', 'createhook');
        xarModHooks::unregister('item', 'update', 'API', 'uploads', 'admin', 'updatehook');
        xarModHooks::unregister('item', 'delete', 'API', 'uploads', 'admin', 'deletehook');
        xarModHooks::unregister('module', 'remove', 'API', 'uploads', 'admin', 'removehook');
    */

    $module = 'uploads';
    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => $module]);
}
