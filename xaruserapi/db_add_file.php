<?php

/**
 *  Adds a file (fileEntry) entry to the database. This entry just contains metadata
 *  about the file and not the actual DATA (contents) of the file.
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer userId         The id of the user whom submitted the file
 *  @param   string  name       The name of the file (minus any path information)
 *  @param   string  location   The complete path to the file including the name (obfuscated if so chosen)
 *  @param   string  type       The mime content-type of the file
 *  @param   integer status     The status of the file (APPROVED, SUBMITTED, READABLE, REJECTED)
 *  @param   integer store_type     The manner in which the file is to be stored (filesystem, database)
 *  @param   array   extrainfo      Extra information to be stored for this file (e.g. modified, width, height, ...)
 *
 *  @returns integer The id of the fileEntry that was added, or FALSE on error
 */

function filemanager_userapi_db_add_file( $args )
{

    $name       = NULL;
    $location   = NULL;
    $realPath       = NULL;
    $userId         = xarSessionGetVar('uid');
    $status     = NULL;
    $size       = NULL;
    $store_type     = _FILEMANAGER_STORE_FILESYSTEM;
    $type       = NULL;
    
    extract($args);

    if (!isset($name)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'name','db_add_file','filemanager');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($destination) || empty($destination)) {
        $msg = xarML('Missing or incorrect parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'location', 'db_add_file', 'filemanager');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    } elseif (!file_exists($destination) || !is_readable($destination)) {
        $msg = xarML('File location at: [#(1)] does not exist or is unreadable', $location);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    // Use the public files folder if no folder was specified
    if (!isset($dirId) || empty($dirId)) {
        $dirId = xarModGetVar('filemanager', 'folders.public-files');
    }
        
    // Grab the actual patch for the xaraya filestore (untrusted files)
    $xarfs = eregi_replace('/$', '', xarModGetVar('filemanager', 'path.untrust'));
    
    // If location has the xarfs path in it, then we
    // need to make location == xarfs://[virtual dir id]/[real name]
    if (stristr($destination, $xarfs)) {
        
        // change location to: xarfs://[virtual dir id]/[real name]
        $location = 'xarfs://' . $dirId . '/' . str_replace("$xarfs/", '', $destination);
    
    } elseif ($dirId = xarModAPIFunc('filemanager', 'mount', 'is_mountpoint', array('path' => $destination))) {
        
        // Otherwise, if it's a file inside a mount point, we
        // set the location to: mount://[mount point id]/[path to file]
        $mountInfo = xarModAPIFunc('filemanager', 'mount', 'get', array('vdir_id' => $dirId));
        $location = 'mount://' . str_replace('//', '/', $dirId . '/' . str_replace($mountInfo['path'], '', $destination));
    } 

// FIXME: this is not synchronised with 1.0.0 branch !
    
    if (!isset($status)) {
        $autoApprove = xarModGetVar('filemanager', 'file.auto-approve');

        if ($autoApprove == _FILEMANAGER_APPROVE_EVERYONE ||
           ($autoApprove == _FILEMANAGER_APPROVE_ADMIN && xarSecurityCheck('AdminFileManager', 0))) {
                $status = _FILEMANAGER_STATUS_APPROVED;
        } else {
            $status = _FILEMANAGER_STATUS_SUBMITTED;
        }
    }

    if (!isset($size)) {
        // if the file size wasn't specified, then try to grab it now
        $size = @size($destination);
    } 
    
    // If the file type wasn't specified, then figure it out here
    if (!isset($type)) {
        $type = xarModAPIFunc('mime','user','analyze_file',
                                   array('name'   => $destination,
                                         'altname'=> $name));
        if (empty($type)) {
            $type = 'application/octet-stream';
        }
    }

    if (empty($extrainfo)) {
        $extrainfo = '';
    } elseif (is_array($extrainfo)) {
        $extrainfo = serialize($extrainfo);
    }

    //add to filemanager table
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();


    // table and column definitions
    $fileEntry_table = $xartable['file_entry'];
    $file_id    = $dbconn->GenID($fileEntry_table);

    // insert value into table
    $sql = "INSERT INTO $fileEntry_table
                      (
                        xar_fileEntry_id,
                        xar_user_id,
                        xar_filename,
                        xar_location,
                        xar_status,
                        xar_filesize,
                        xar_store_type,
                        xar_mime_type,
                        xar_extrainfo
                      )
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $bindvars = array((int) $file_id,
                      (int) $userId,
                      (string) $name,
                      (string) $location,
                      (int) $status,
                      (int) $size,
                      (int) $store_type,
                      (string) $type,
                      (string) $extrainfo);

    $result = &$dbconn->Execute($sql, $bindvars);


    if (!$result) {
        return FALSE;
    }

    $fileId = $dbconn->PO_Insert_ID($xartable['file_entry'], 'xar_fileEntry_id');

    // Pass the arguments to the hook modules too
    $args['module'] = 'filemanager';
    $args['itemtype'] = 1; // Files
    xarModCallHooks('item', 'create', $fileId, $args);

    return $fileId;
}

?>
