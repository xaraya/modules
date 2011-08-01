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
    if (!xarModIsAvailable('mime')) {
        $msg = xarML('The mime module should be activated first');
        throw new Exception($msg);             
    }

    // load the predefined constants
    xarModAPILoad('uploads', 'user');

    if(xarServer::getVar('SCRIPT_FILENAME')) {
        $base_directory = dirname(realpath(xarServer::getVar('SCRIPT_FILENAME')));
    } else {
        $base_directory = './';
    }
    xarModVars::set('uploads', 'uploads_directory',   'Change me to something outside the webroot');
    xarModVars::set('uploads', 'imports_directory',   'Change me to something outside the webroot');
    xarModVars::set('uploads', 'file.maxsize',            '10000000');
    xarModVars::set('uploads', 'file.delete-confirmation', TRUE);
    xarModVars::set('uploads', 'file.auto-purge',          FALSE);
    xarModVars::set('uploads', 'file.obfuscate-on-import', FALSE);
    xarModVars::set('uploads', 'file.obfuscate-on-upload', TRUE);
    xarModVars::set('uploads', 'path.imports-cwd', xarModVars::get('uploads', 'imports_directory'));
    xarModVars::set('uploads', 'dd.fileupload.stored',   TRUE);
    xarModVars::set('uploads', 'dd.fileupload.external', TRUE);
    xarModVars::set('uploads', 'dd.fileupload.upload',   TRUE);
    xarModVars::set('uploads', 'dd.fileupload.trusted',  TRUE);
    xarModVars::set('uploads', 'file.auto-approve', _UPLOADS_APPROVE_ADMIN);

    $data['filters']['inverse']                     = FALSE;
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
    $mimetypes += xarModAPIFunc('mime','user','getall_types');

    xarModVars::set('uploads','view.filter', serialize(array('data' => $data,'filter' => $filter)));
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

    $file_entry_fields = array(
        'xar_fileEntry_id' => array('type'=>'integer', 'size'=>'big', 'null'=>FALSE,  'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_user_id'      => array('type'=>'integer', 'size'=>'big', 'null'=>FALSE),
        'xar_filename'     => array('type'=>'varchar', 'size'=>128,   'null'=>FALSE),
        'xar_location'     => array('type'=>'varchar', 'size'=>255,   'null'=>FALSE),
        'xar_status'       => array('type'=>'integer', 'size'=>'tiny','null'=>FALSE,  'default'=>'0'),
        'xar_filesize'     => array('type'=>'integer', 'size'=>'big',    'null'=>FALSE),
        'xar_store_type'   => array('type'=>'integer', 'size'=>'tiny',     'null'=>FALSE),
        'xar_mime_type'    => array('type'=>'varchar', 'size' =>128,  'null'=>FALSE,  'default' => 'application/octet-stream'),
        'xar_extrainfo'    => array('type'=>'text')
    );


    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $sql is empty
    $query   =  xarDBCreateTable($file_entry_table, $file_entry_fields);
    $result  =& $dbconn->Execute($query);

    $file_data_fields = array(
        'xar_fileData_id'  => array('type'=>'integer','size'=>'big','null'=>FALSE,'increment'=>TRUE, 'primary_key'=>TRUE),
        'xar_fileEntry_id' => array('type'=>'integer','size'=>'big','null'=>FALSE),
        'xar_fileData'     => array('type'=>'blob','size'=>'medium','null'=>FALSE)
    );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $sql is empty
    $query  =  xarDBCreateTable($file_data_table, $file_data_fields);
    $result =& $dbconn->Execute($query);

    $file_assoc_fields = array(
        'xar_fileEntry_id' => array('type'=>'integer', 'size'=>'big', 'null'=>FALSE),
        'xar_modid'        => array('type'=>'integer', 'size'=>'big', 'null'=>FALSE),
        'xar_itemtype'     => array('type'=>'integer', 'size'=>'big', 'null'=>FALSE, 'default'=>'0'),
        'xar_objectid'       => array('type'=>'integer', 'size'=>'big', 'null'=>FALSE, 'default'=>'0'),
    );


    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $sql is empty
    $query   =  xarDBCreateTable($file_assoc_table, $file_assoc_fields);
    $result  =& $dbconn->Execute($query);

    $instances[0]['header'] = 'external';
    $instances[0]['query']  = xarModURL('uploads', 'admin', 'privileges');
    $instances[0]['limit']  = 0;

    xarDefineInstance('uploads', 'File', $instances);

    xarRegisterMask('ViewUploads',  'All','uploads','File','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadUploads',  'All','uploads','File','All','ACCESS_READ');
    xarRegisterMask('EditUploads',  'All','uploads','File','All','ACCESS_EDIT');
    xarRegisterMask('AddUploads',   'All','uploads','File','All','ACCESS_ADD');
    xarRegisterMask('ManageUploads','All','uploads','File','All','ACCESS_DELETE');
    xarRegisterMask('AdminUploads', 'All','uploads','File','All','ACCESS_ADMIN');

    xarRegisterPrivilege('ViewUploads',  'All','uploads','File','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadUploads',  'All','uploads','File','All','ACCESS_READ');
    xarRegisterPrivilege('EditUploads',  'All','uploads','File','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddUploads',   'All','uploads','File','All','ACCESS_ADD');
    xarRegisterPrivilege('ManageUploads','All','uploads','File','All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminUploads', 'All','uploads','File','All','ACCESS_ADMIN');

    /**
     * Register hooks
     */
    if (!xarModRegisterHook('item', 'transform', 'API', 'uploads', 'user', 'transformhook')) {
         $msg = xarML('Could not register hook');
         throw new Exception($msg);
    }
/*
    if (!xarModRegisterHook('item', 'create', 'API', 'uploads', 'admin', 'createhook')) {
         $msg = xarML('Could not register hook');
        throw new Exception($msg);             
    }
    if (!xarModRegisterHook('item', 'update', 'API', 'uploads', 'admin', 'updatehook')) {
         $msg = xarML('Could not register hook');
        throw new Exception($msg);             
    }
    if (!xarModRegisterHook('item', 'delete', 'API', 'uploads', 'admin', 'deletehook')) {
         $msg = xarML('Could not register hook');
        throw new Exception($msg);             
    }
    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('module', 'remove', 'API', 'uploads', 'admin', 'removehook')) {
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
    switch($oldversion) {
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
    xarModUnregisterHook('item', 'transform', 'API', 'uploads', 'user', 'transformhook');
/*
    xarModUnregisterHook('item', 'create', 'API', 'uploads', 'admin', 'createhook');
    xarModUnregisterHook('item', 'update', 'API', 'uploads', 'admin', 'updatehook');
    xarModUnregisterHook('item', 'delete', 'API', 'uploads', 'admin', 'deletehook');
    xarModUnregisterHook('module', 'remove', 'API', 'uploads', 'admin', 'removehook');
*/

    $module = 'uploads';
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>
