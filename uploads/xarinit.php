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

    if(xarServerGetVar('PATH_TRANSLATED')) {
        $base_directory = dirname(realpath(xarServerGetVar('PATH_TRANSLATED')));
    } elseif(xarServerGetVar('SCRIPT_FILENAME')) {
        $base_directory = dirname(realpath(xarServerGetVar('SCRIPT_FILENAME')));
    } else {
        $base_directory = './';
    }
    xarModSetVar('uploads', 'path.uploads-directory',   $base_directory . 'var/uploads');
    xarModSetVar('uploads', 'path.imports-directory',   $base_directory . 'var/imports');
    xarModSetVar('uploads', 'file.maxsize',             '10000000');
    xarModSetVar('uploads', 'file.censored-mimetypes',   serialize(array()));
    xarModSetVar('uploads', 'file.delete-confirmation',  TRUE);
    xarModSetVar('uploads', 'file.obfuscate-on-import',  FALSE);
    xarModSetVar('uploads', 'file.obfuscate-on-upload',  TRUE);
        
    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    
    $uploadstable = $xartable['uploads'];
    $blobstable = $xartable['uploads_blobs'];

    xarDBLoadTableMaintenanceAPI();
    $uploadsfields = array(
        'xar_file_id'    => array('type'=>'integer', 'size'=>32      'null'=>FALSE,  'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_userid'     => array('type'=>'integer', 'size'=>32,     'null'=>FALSE),
        'xar_filename'   => array('type'=>'varchar', 'size'=>254,    'null'=>FALSE),
        'xar_location'   => array('type'=>'varchar', 'size'=>254,    'null'=>FALSE),
        'xar_status'     => array('type'=>'tinyint', 'size'=>3,      'null'=>FALSE,  'default'=>'0'),
        'xar_filesize'   => array('type'=>'integer', 'size'=>64,     'null'=>FALSE);
        'xar_store_type' => array('type'=>'char',    'size'=>1,      'null'=>FALSE),
        'xar_mime_type'  => array('type'=>'varchar', 'size' => 128,  'null'=>FALSE,  'default' => 'application/octet-stream')
    );
        
        
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $sql is empty
    $query = xarDBCreateTable($uploadstable,$uploadsfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    //now create the blob table to contain images & files in the database.
    /*CREATE TABLE `xar_uploadblobs` (
        `xar_ulbid` INT( 32 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `xar_ulid` INT( 32 ) UNSIGNED NOT NULL ,
        `xar_ulblob` BLOB NOT NULL ,
    INDEX ( `xar_ulid` ) 
     );*/

    $blobsfields = array(
        'xar_upload_blob_id' =>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_file_id'      =>array('type'=>'varchar','size'=>32,'null'=>FALSE),
        'xar_ulblob'         =>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0')
    );
        
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $sql is empty
    $query = xarDBCreateTable($blobstable,$blobsfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    
    xarRegisterMask('ViewUploads','All','uploads','Upload','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadUploads','All','uploads','Upload','All','ACCESS_READ');
    xarRegisterMask('EditUploads','All','uploads','Upload','All','ACCESS_EDIT');
    xarRegisterMask('AdminUploads','All','uploads','Upload','All','ACCESS_ADMIN');
    

    /**
     * Register hooks
     */
    // Set up module hooks
    if (!xarModRegisterHook('item', 'transform', 'API',
                           'uploads', 'user', 'transformhook')) {
         $msg = xarML('Could not register hook');
         xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
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
        case .01:
        case .02:
            // change newhook from API to GUI
            list($dbconn) = xarDBGetConn();

            $hookstable = xarDBGetSiteTablePrefix() . '_hooks';
            $query = "UPDATE $hookstable
                      SET xar_tarea='GUI'
                      WHERE xar_tmodule='uploads' AND xar_tfunc='newhook'";

            $result =& $dbconn->Execute($query);
            if (!$result) return;
        case .03:
            // Remove unused hooks
            xarModUnregisterHook('item', 'new', 'GUI','uploads', 'admin', 'newhook');
            xarModUnregisterHook('item', 'create', 'API', 'uploads', 'admin', 'createhook');
            xarModUnregisterHook('item', 'display', 'GUI', 'uploads', 'user', 'formdisplay');
            
            
            // Had problems with unregister not working in beta testing... So forcefully removing these
            list($dbconn) = xarDBGetConn();
        
            $hookstable = xarDBGetSiteTablePrefix() . '_hooks';
            $query = "DELETE FROM $hookstable
                      WHERE xar_tmodule='uploads' AND (xar_tfunc='formdisplay' OR xar_tfunc='createhook' OR xar_tfunc='newhook')";
        
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            
            break;
        case .04:
        case .05:
        default:
            //Add mimetype column to DB
//            ALTER TABLE `xar_uploads` ADD `ulmime` VARCHAR( 128 ) DEFAULT 'application/octet-stream' NOT NULL ;

            // Get database information
            list($dbconn) = xarDBGetConn();
            $xartable = xarDBGetTables();
            $linkagetable = $xartable['uploads'];

            xarDBLoadTableMaintenanceAPI();

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

    }
    return true;
}

/**
 * delete the uploads module
 */
function uploads_delete()
{
    xarModDelVar('uploads', 'uploads_directory');
    xarModDelVar('uploads', 'maximum_upload_size');
    xarModDelVar('uploads', 'allowed_types');

    // Get database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    //Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['uploads']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['blobs']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

?>
