<?php
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

            $xartable =& xarDBGetTables();
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
            $path_uploads_directory   = xarModGetVar('uploads','uploads_directory');
            if (empty($path_uploads_directory)) {
                $path_uploads_directory = $base_directory . '/var/uploads';
            }

            $path_imports_directory   = xarModGetVar('uploads','import_directory');
            if (empty($import_directory)) {
               $path_imports_directory = $base_directory . '/var/imports';
            }

            $file_maxsize             = xarModGetVar('uploads','maximum_upload_size');
            $file_censored_mimetypes  = serialize(array('application','video','audio', 'other', 'message'));
            $file_delete_confirmation = xarModGetVar('uploads','confirm_delete') ? 1 : 0;
            $file_obfuscate_on_import = xarModGetVar('uploads','obfuscate_imports') ? 1 : 0;
            $file_obfuscate_on_upload = TRUE;

            // Now remove the old module vars
            xarModDelVar('uploads','uploads_directory');
            xarModDelVar('uploads','maximum_upload_size');
            xarModDelVar('uploads','allowed_types');
            xarModDelVar('uploads','confirm_delete');
            xarModDelVar('uploads','max_image_width');
            xarModDelVar('uploads','max_image_height');
            xarModDelVar('uploads','thumbnail_setting');
            xarModDelVar('uploads','thumbnail_path');
            xarModDelVar('uploads','netpbm_path');
            xarModDelVar('uploads','import_directory');
            xarModDelVar('uploads','obfuscate_imports');

            // Now set up the new ones :)
            xarModSetVar('uploads','path.uploads-directory', $path_uploads_directory);
            xarModSetVar('uploads','path.imports-directory', $path_imports_directory);
            xarModSetVar('uploads','file.maxsize', ($file_maxsize >= 0) ? $file_maxsize : 1000000);
            xarModSetVar('uploads','file.obfuscate-on-import', ($file_obfuscate_on_import) ? $file_obfuscate_on_import : FALSE);
            xarModSetVar('uploads','file.obfuscate-on-upload', ($file_obfuscate_on_upload) ? $file_obfuscate_on_upload : FALSE);
            xarModSetVar('uploads','file.delete-confirmation', ($file_delete_confirmation) ? $file_delete_confirmation : FALSE);
            xarModSetVar('uploads','file.auto-purge',          FALSE);
            xarModSetVar('uploads','path.imports-cwd', xarModGetVar('uploads', 'path.imports-directory'));
            xarModSetVar('uploads', 'dd.fileupload.stored',   TRUE);
            xarModSetVar('uploads', 'dd.fileupload.external', TRUE);
            xarModSetVar('uploads', 'dd.fileupload.upload',   TRUE);
            xarModSetVar('uploads', 'dd.fileupload.trusted',  TRUE);

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

            xarModSetVar('uploads','view.filter', serialize(array('data' => $data,'filter' => $filter)));

            xarDBLoadTableMaintenanceAPI();

            $dbconn =& xarDBGetConn();

            $xartables           =& xarDBGetTables();

            $uploads_table       = xarDBGetSiteTablePrefix() . "_uploads";
            $uploads_blobs_table = xarDBGetSiteTablePrefix() . "_uploadblobs";

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

?>
