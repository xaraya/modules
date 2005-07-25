<?php

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
        xarErrorSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY', new SystemException($msg));
        return;
    }

    // load the predefined constants
    xarModAPILoad('uploads', 'user');

    if(xarServerGetVar('SCRIPT_FILENAME')) {
        $base_directory = dirname(realpath(xarServerGetVar('SCRIPT_FILENAME')));
    } else {
        $base_directory = './';
    }
    xarModSetVar('uploads', 'path.untrust',   xarML('Change me!!'));
    xarModSetVar('uploads', 'path.trusted',   xarML('Change me!!'));
    xarModSetVar('uploads', 'file.maxsize',            '10000000');
    xarModSetVar('uploads', 'file.delete-confirmation', TRUE);
    xarModSetVar('uploads', 'file.auto-purge',          FALSE);
    xarModSetVar('uploads', 'path.cwd', xarModGetVar('uploads', 'path.trusted'));
    xarModSetVar('uploads', 'dd.fileupload.stored',   TRUE);
    xarModSetVar('uploads', 'dd.fileupload.external', TRUE);
    xarModSetVar('uploads', 'dd.fileupload.upload',   TRUE);
    xarModSetVar('uploads', 'dd.fileupload.trusted',  TRUE);
    xarModSetVar('uploads', 'file.auto-approve', _UPLOADS_APPROVE_ADMIN);
    xarModGetVar('uploads', 'db.blocksize', (64 * 1024));

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

    xarModSetVar('uploads','view.filter', serialize(array('data' => $data,'filter' => $filter)));
    unset($mimetypes);

    // Get datbase setup
    $dbconn =& xarDBGetConn();

    $xartable =& xarDBGetTables();

    $file_entry_table = $xartable['file_entry'];
    $file_data_table  = $xartable['file_data'];
    $file_assoc_table = $xartable['file_associations'];

    xarDBLoadTableMaintenanceAPI();

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

    xarRegisterMask('ViewUploads',  'All','uploads','File','All:All:All:All','ACCESS_READ');
    xarRegisterMask('AddUploads',   'All','uploads','File','All:All:All:All','ACCESS_ADD');
    xarRegisterMask('EditUploads',  'All','uploads','File','All:All:All:All','ACCESS_EDIT');
    xarRegisterMask('DeleteUploads','All','uploads','File','All:All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminUploads', 'All','uploads','File','All:All:All:All','ACCESS_ADMIN');

    /**
     * Register hooks
     */
    if (!xarModRegisterHook('item', 'transform', 'API', 'uploads', 'user', 'transformhook')) {
         $msg = xarML('Could not register hook');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }


    if (xarCurrentErrorType() !== XAR_NO_EXCEPTION) {
        // if there was an error, make sure to remove the tables
        // so the user can try the install again
        uploads_delete();
        return;
    }

    return true;
}

/**
 * upgrade the uploads module from an old version
 */
/**
 * upgrade the articles module from an old version
 */
function uploads_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.10':
        case '0.1.0':
            include_once("modules/uploads/upgrades/0.1.0.php");

        case '0.7.5':
            xarModAPILoad('uploads', 'user');
            xarModSetVar('uploads', 'file.auto-approve', _UPLOADS_APPROVE_ADMIN);

        case '0.9.8': // last version of uploads module before 1.0.0 
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $file_entry_table = $xartable['file_entry'];
            xarDBLoadTableMaintenanceAPI();
            $query = xarDBAlterTable($file_entry_table,
                                     array('command' => 'add',
                                           'field' => 'xar_extrainfo',
                                           'type' => 'text'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;

        case '1.0.0': // current version of uploads module
            include_once("modules/uploads/upgrades/0.9.8.php");

        /**
         * continue upgrades from uploads module here
         */

            break;

        case '0.9.9': // last version of uploads_guimods module before 2.0.0
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $file_entry_table = $xartable['file_entry'];
            xarDBLoadTableMaintenanceAPI();
            $query = xarDBAlterTable($file_entry_table,
                                     array('command' => 'add',
                                           'field' => 'xar_extrainfo',
                                           'type' => 'text'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;

        case '2.0.0': // current version of uploads_guimods module

        /**
         * continue upgrades from uploads_guimods module here
         */

            break;

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
    xarModDelVar('uploads', 'path.untrust');
    xarModDelVar('uploads', 'path.trusted');
    xarModDelVar('uploads', 'file.maxsize');
    xarModDelVar('uploads', 'file.delete-confirmation');
    xarModDelVar('uploads', 'file.auto-purge');
    xarModDelVar('uploads', 'path.cwd');
    xarModDelVar('uploads', 'dd.fileupload.stored');
    xarModDelVar('uploads', 'dd.fileupload.external');
    xarModDelVar('uploads', 'dd.fileupload.upload');
    xarModDelVar('uploads', 'dd.fileupload.trusted');
    xarModDelVar('uploads', 'file.auto-approve');
    xarModDelVar('uploads', 'view.filter');

    xarUnregisterMask('ViewUploads');
    xarUnregisterMask('AddUploads');
    xarUnregisterMask('EditUploads');
    xarUnregisterMask('DeleteUploads');
    xarUnregisterMask('AdminUploads');

    xarModUnregisterHook('item', 'transform', 'API', 'uploads', 'user', 'transformhook');

    // Get database information

    $dbconn =& xarDBGetConn();
    $xartables      =& xarDBGetTables();

    //Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartables['file_entry']);
    if (empty($query))
        return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    // handle any exception
    xarErrorHandled();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartables['file_data']);
    if (empty($query))
        return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    xarErrorHandled();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartables['file_associations']);
    if (empty($query))
        return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    xarErrorHandled();

    return true;
}

?>
