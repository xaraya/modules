<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
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
    xarModVars::set('uploads', 'path.uploads-directory',   'Change me to something outside the webroot');
    xarModVars::set('uploads', 'path.imports-directory',   'Change me to something outside the webroot');
    xarModVars::set('uploads', 'file.maxsize',            '10000000');
    xarModVars::set('uploads', 'file.delete-confirmation', TRUE);
    xarModVars::set('uploads', 'file.auto-purge',          FALSE);
    xarModVars::set('uploads', 'file.obfuscate-on-import', FALSE);
    xarModVars::set('uploads', 'file.obfuscate-on-upload', TRUE);
    xarModVars::set('uploads', 'path.imports-cwd', xarModVars::get('uploads', 'path.imports-directory'));
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

    xarModVars::set('uploads', 'view.itemsperpage', 200);
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
         throw new Exception($msg);
    }
/*
    if (!xarModRegisterHook('item', 'create', 'API', 'uploads', 'admin', 'createhook')) {
         $msg = xarML('Could not register hook');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }
    if (!xarModRegisterHook('item', 'update', 'API', 'uploads', 'admin', 'updatehook')) {
         $msg = xarML('Could not register hook');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }
    if (!xarModRegisterHook('item', 'delete', 'API', 'uploads', 'admin', 'deletehook')) {
         $msg = xarML('Could not register hook');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }
    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('module', 'remove', 'API', 'uploads', 'admin', 'removehook')) {
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
        case '0.0.1':
        case '0.01':
        case '0.0.2':
        case '0.02':
            // change newhook from API to GUI

           $dbconn = xarDB::getConn();

            $hookstable = xarDB::getPrefix() . '_hooks';
            $query = "UPDATE $hookstable
                      SET xar_tarea='GUI'
                      WHERE xar_tmodule='uploads' AND xar_tfunc='newhook'";

            $result =& $dbconn->Execute($query);
            if (!$result) return;
        case '0.0.3':
        case '0.03':
            // Remove unused hooks
            xarModUnregisterHook('item', 'new', 'GUI','uploads', 'admin', 'newhook');
            xarModUnregisterHook('item', 'create', 'API', 'uploads', 'admin', 'createhook');
            xarModUnregisterHook('item', 'display', 'GUI', 'uploads', 'user', 'formdisplay');


            // Had problems with unregister not working in beta testing... So forcefully removing these
            $dbconn = xarDB::getConn();

            $hookstable = xarDB::getPrefix() . '_hooks';
            $query = "DELETE FROM $hookstable
                            WHERE xar_tmodule='uploads'
                              AND (xar_tfunc='formdisplay'
                               OR xar_tfunc='createhook'
                               OR xar_tfunc='newhook')";

            $result =& $dbconn->Execute($query);
            if (!$result) return;

        case '0.0.4':
        case '0.04':
        case '0.0.5':
        case '0.05':
            //Add mimetype column to DB
//            ALTER TABLE `xar_uploads` ADD `ulmime` VARCHAR( 128 ) DEFAULT 'application/octet-stream' NOT NULL ;

            // Get database information
            $dbconn = xarDB::getConn();

            $xartable = xarDB::getTables();
            $linkagetable =& $xartable['uploads'];

            sys::import('xaraya.tableddl');
            /*
            // If we're here, then don't worry about altering the table
            // we'll generate the mime type later

            // add the xar_itemtype column
            $query = xarDBAlterTable($linkagetable,
                                     array('command' => 'add',
                                           'field' => 'xar_ulmime',
                                           'type' => 'varchar',
                                           'size' => 128,
                                           'null' => false,
                                           'default' => 'application/octet-stream'));
            $result = &$dbconn->Execute($query);
            if (!$result) return;
               */
        case '0.10':
        case '0.1.0':

            //Not needed anymore with the dependency checks.
            if (!xarModIsAvailable('mime')) {
                $msg = xarML('The mime module should be activated first');
                xarErrorSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY', new SystemException($msg));
                return;
            }

            xarModAPILoad('uploads','user');

            xarRemoveMasks('uploads');
            xarRemoveInstances('uploads');

            xarRegisterMask('ViewUploads',  'All','uploads','File','All:All:All:All','ACCESS_READ');
            xarRegisterMask('AddUploads',   'All','uploads','File','All:All:All:All','ACCESS_ADD');
            xarRegisterMask('EditUploads',  'All','uploads','File','All:All:All:All','ACCESS_EDIT');
            xarRegisterMask('DeleteUploads','All','uploads','File','All:All:All:All','ACCESS_DELETE');
            xarRegisterMask('AdminUploads', 'All','uploads','File','All:All:All:All','ACCESS_ADMIN');

            $xartable = xarDB::getTables();
            $instances[0]['header'] = 'external';
            $instances[0]['query']  = xarModURL('uploads', 'admin', 'privileges');
            $instances[0]['limit']  = 0;

            xarDefineInstance('uploads', 'File', $instances);

            if (xarServerGetVar('SCRIPT_FILENAME')) {
                $base_directory = dirname(realpath(xarServerGetVar('SCRIPT_FILENAME')));
            } else {
                $base_directory = './';
            }

            // Grab the old values
            $path_uploads_directory   = xarModVars::get('uploads','uploads_directory');
            if (empty($path_uploads_directory)) {
                $path_uploads_directory = $base_directory . '/var/uploads';
            }

            $path_imports_directory   = xarModVars::get('uploads','import_directory');
            if (empty($import_directory)) {
               $path_imports_directory = $base_directory . '/var/imports';
            }

            $file_maxsize             = xarModVars::get('uploads','maximum_upload_size');
            $file_censored_mimetypes  = serialize(array('application','video','audio', 'other', 'message'));
            $file_delete_confirmation = xarModVars::get('uploads','confirm_delete') ? 1 : 0;
            $file_obfuscate_on_import = xarModVars::get('uploads','obfuscate_imports') ? 1 : 0;
            $file_obfuscate_on_upload = TRUE;

            // Now remove the old module vars
            xarModVars::delete('uploads','uploads_directory');
            xarModVars::delete('uploads','maximum_upload_size');
            xarModVars::delete('uploads','allowed_types');
            xarModVars::delete('uploads','confirm_delete');
            xarModVars::delete('uploads','max_image_width');
            xarModVars::delete('uploads','max_image_height');
            xarModVars::delete('uploads','thumbnail_setting');
            xarModVars::delete('uploads','thumbnail_path');
            xarModVars::delete('uploads','netpbm_path');
            xarModVars::delete('uploads','import_directory');
            xarModVars::delete('uploads','obfuscate_imports');

            // Now set up the new ones :)
            xarModVars::set('uploads','path.uploads-directory', $path_uploads_directory);
            xarModVars::set('uploads','path.imports-directory', $path_imports_directory);
            xarModVars::set('uploads','file.maxsize', ($file_maxsize >= 0) ? $file_maxsize : 1000000);
            xarModVars::set('uploads','file.obfuscate-on-import', ($file_obfuscate_on_import) ? $file_obfuscate_on_import : FALSE);
            xarModVars::set('uploads','file.obfuscate-on-upload', ($file_obfuscate_on_upload) ? $file_obfuscate_on_upload : FALSE);
            xarModVars::set('uploads','file.delete-confirmation', ($file_delete_confirmation) ? $file_delete_confirmation : FALSE);
            xarModVars::set('uploads','file.auto-purge',          FALSE);
            xarModVars::set('uploads','path.imports-cwd', xarModVars::get('uploads', 'path.imports-directory'));
            xarModVars::set('uploads', 'dd.fileupload.stored',   TRUE);
            xarModVars::set('uploads', 'dd.fileupload.external', TRUE);
            xarModVars::set('uploads', 'dd.fileupload.upload',   TRUE);
            xarModVars::set('uploads', 'dd.fileupload.trusted',  TRUE);

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
            unset($mimetypes);

            xarModVars::set('uploads','view.filter', serialize(array('data' => $data,'filter' => $filter)));

            sys::import('xaraya.tableddl');

            $dbconn = xarDB::getConn();

            $xartables           = xarDB::getTables();

            $uploads_table       = xarDB::getPrefix() . "_uploads";
            $uploads_blobs_table = xarDB::getPrefix() . "_uploadblobs";

            $file_entry_table    =& $xartables['file_entry'];
            $file_assoc_table    =& $xartables['file_associations'];
            $file_data_table     =& $xartables['file_data'];


            // Grab all the file entries from the db
            $query = "SELECT xar_ulid,
                             xar_uluid,
                             xar_ulfile,
                             xar_ulhash,
                             xar_ulapp,
                             xar_ultype
                        FROM $uploads_table";

            $result  =& $dbconn->Execute($query);
            if (!$result)
                return;

            $fileEntries = array();

            while (!$result->EOF) {
                $row = $result->GetRowAssoc(false);
                $entry['xar_fileEntry_id']  = $row['xar_ulid'];
                $entry['xar_user_id']       = $row['xar_uluid'];
                $entry['xar_filename']      = $row['xar_ulfile'];
                $entry['xar_location']      = $path_uploads_directory . '/' . $row['xar_ulhash'];

                // If the file doesn't exist, then skip the entry
                // no reason to add a 'dead' file
                if (!file_exists($entry['xar_location'])) {
                    $result->MoveNext();
                    continue;
                }

                $entry['xar_status']        = ($row['xar_ulapp']) ? _UPLOADS_STATUS_APPROVED : _UPLOADS_STATUS_SUBMITTED;
                $entry['xar_filesize']      = @filesize($entry['xar_location']) ? @filesize($entry['xar_location']) : 0;

                switch(strtolower($row['xar_ultype'])) {
                    case 'd':
                                $entry['xar_store_type'] = _UPLOADS_STORE_DB_FULL;
                                break;
                    default:
                    case 'f':
                                $entry['xar_store_type'] = _UPLOADS_STORE_FSDB;
                                break;
                }
                $entry['xar_mime_type']     = xarModAPIFunc('mime','user','analyze_file', array('fileName' => $entry['xar_location']));
                $fileEntries[] = $entry;
                $result->MoveNext();
            }

            // Create the new tables
            $file_entry_fields = array(
                'xar_fileEntry_id' => array('type'=>'integer', 'size'=>'big', 'null'=>FALSE,  'increment'=>TRUE,'primary_key'=>TRUE),
                'xar_user_id'      => array('type'=>'integer', 'size'=>'big', 'null'=>FALSE),
                'xar_filename'     => array('type'=>'varchar', 'size'=>128,   'null'=>FALSE),
                'xar_location'     => array('type'=>'varchar', 'size'=>255,   'null'=>FALSE),
                'xar_status'       => array('type'=>'integer', 'size'=>'tiny','null'=>FALSE,  'default'=>'0'),
                'xar_filesize'     => array('type'=>'integer', 'size'=>'big', 'null'=>FALSE),
                'xar_store_type'   => array('type'=>'integer', 'size'=>'tiny','null'=>FALSE),
                'xar_mime_type'    => array('type'=>'varchar', 'size' =>128,  'null'=>FALSE,  'default' => 'application/octet-stream')
            );


            // Create the Table - the function will return the SQL is successful or
            // raise an exception if it fails, in this case $sql is empty
            $query   =  xarDBCreateTable($file_entry_table, $file_entry_fields);
            $result  =& $dbconn->Execute($query);
            if (!$result) {
                $query = xarDBDropTable($file_entry_table);
                $result =& $dbconn->Execute($query);
                return;
            }

            // Add files to new database
            foreach ($fileEntries as $fileEntry) {
                $query = "INSERT INTO $file_entry_table
                                    (
                                      xar_fileEntry_id,
                                      xar_user_id,
                                      xar_filename,
                                      xar_location,
                                      xar_status,
                                      xar_filesize,
                                      xar_store_type,
                                      xar_mime_type
                                    )
                               VALUES
                                    (
                                      $fileEntry[xar_fileEntry_id],
                                      $fileEntry[xar_user_id],
                                     '$fileEntry[xar_filename]',
                                     '$fileEntry[xar_location]',
                                      $fileEntry[xar_status],
                                      $fileEntry[xar_filesize],
                                      $fileEntry[xar_store_type],
                                     '$fileEntry[xar_mime_type]'
                                    )";
                $result =& $dbconn->Execute($query);
                if (!$result) {
                    $query = xarDBDropTable($file_entry_table);
                    $result =& $dbconn->Execute($query);
                    return;
                }
            }

            $file_data_fields = array(
                'xar_fileData_id'  => array('type'=>'integer','size'=>'big','null'=>FALSE,'increment'=>TRUE, 'primary_key'=>TRUE),
                'xar_fileEntry_id' => array('type'=>'integer','size'=>'big','null'=>FALSE),
                'xar_fileData'     => array('type'=>'blob','size'=>'medium','null'=>FALSE)
            );

            // Create the Table - the function will return the SQL is successful or
            // raise an exception if it fails, in this case $sql is empty
            $query  =  xarDBCreateTable($file_data_table, $file_data_fields);
            $result =& $dbconn->Execute($query);
            if (!$result) {
                // if there was an error, make sure to remove the tables
                // so the user can try the upgrade again
                $query[] = xarDBDropTable($file_entry_table);
                $query[] = xarDBDropTable($file_data_table);
                foreach ($query as $run) {
                    $result =& $dbconn->Execute($run);
                }
                return;
            }

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
            if (!$result) {
                // if there was an error, make sure to remove the tables
                // so the user can try the upgrade again
                $query[] = xarDBDropTable($file_entry_table);
                $query[] = xarDBDropTable($file_data_table);
                $query[] = xarDBDropTable($file_assoc_table);
                foreach ($query as $run) {
                    $result =& $dbconn->Execute($run);
                }
                return;
            }

            /**
             * Last, but not least, we drop the old tables:
             * We wait to do this until the very end so that, in the event there
             * was a problem, we can retry at some point in time
             */
            $query = xarDBDropTable($uploads_blobs_table);
            $result =& $dbconn->Execute($query);
            if (!$result)
                return;

            $query = xarDBDropTable($uploads_table);
            $result =& $dbconn->Execute($query);
            if (!$result)
                return;


        case '0.7.5':
            xarModAPILoad('uploads', 'user');
            xarModVars::set('uploads', 'file.auto-approve', _UPLOADS_APPROVE_ADMIN);

        case '0.9.8':
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();
            $file_entry_table = $xartable['file_entry'];
            sys::import('xaraya.tableddl');
            $query = xarDBAlterTable($file_entry_table,
                                     array('command' => 'add',
                                           'field' => 'xar_extrainfo',
                                           'type' => 'text'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            xarModVars::set('uploads', 'view.itemsperpage', 200);
            xarModVars::set('uploads', 'file.cache-expire', 0);
            xarModVars::set('uploads', 'file.allow-duplicate-upload', 0);

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
    xarModVars::delete('uploads', 'path.uploads-directory');
    xarModVars::delete('uploads', 'path.imports-directory');
    xarModVars::delete('uploads', 'file.maxsize');
    xarModVars::delete('uploads', 'file.delete-confirmation');
    xarModVars::delete('uploads', 'file.auto-purge');
    xarModVars::delete('uploads', 'file.obfuscate-on-import');
    xarModVars::delete('uploads', 'file.obfuscate-on-upload');
    xarModVars::delete('uploads', 'path.imports-cwd');
    xarModVars::delete('uploads', 'dd.fileupload.stored');
    xarModVars::delete('uploads', 'dd.fileupload.external');
    xarModVars::delete('uploads', 'dd.fileupload.upload');
    xarModVars::delete('uploads', 'dd.fileupload.trusted');
    xarModVars::delete('uploads', 'file.auto-approve');
    xarModVars::delete('uploads', 'view.filter');
    xarModVars::delete('uploads', 'view.itemsperpage');
    xarModVars::delete('uploads', 'file.cache-expire');
    xarModVars::delete('uploads', 'file.allow-duplicate-upload');

    xarUnregisterMask('ViewUploads');
    xarUnregisterMask('AddUploads');
    xarUnregisterMask('EditUploads');
    xarUnregisterMask('DeleteUploads');
    xarUnregisterMask('AdminUploads');

    xarModUnregisterHook('item', 'transform', 'API', 'uploads', 'user', 'transformhook');
/*
    xarModUnregisterHook('item', 'create', 'API', 'uploads', 'admin', 'createhook');
    xarModUnregisterHook('item', 'update', 'API', 'uploads', 'admin', 'updatehook');
    xarModUnregisterHook('item', 'delete', 'API', 'uploads', 'admin', 'deletehook');
    xarModUnregisterHook('module', 'remove', 'API', 'uploads', 'admin', 'removehook');
*/

    // Get database information

    $dbconn = xarDB::getConn();
    $xartables      = xarDB::getTables();

    //Load Table Maintainance API
    sys::import('xaraya.tableddl');

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
