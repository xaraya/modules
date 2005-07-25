<?php

    /**
     * Notes:
     *
     *  Stored files must be owned and writable by the apache process at the file system level
     *  so that the upgrade process can rename them and move them into position.
     */

    // when a module item is deleted
    if (!xarModRegisterHook('item', 'delete', 'API', 'uploads', 'admin', 'deletehook')) {
        return false;
    }

    // Hook the uploads module to the categories module so it
    // can receive events (ie: deletions) from it
    if (!xarModIsHooked('categories', 'uploads')) {
        xarModAPIFunc('modules', 'admin', 'enablehooks',
                       array('callerModName' => 'categories',
                             'hookModName'   => 'uploads'));
    }

    xarModAPILoad('uploads', 'user');
    // Initialize the virtual directories
    if (!xarModAPIFunc('uploads', 'vdir', 'init', array('initLevel' => _UPLOADS_VDIR_ALL))) {
        return FALSE;
    }

    $publicDir = xarModGetVar('uploads', 'folders.public-files');

    $imagesDir  = xarModAPIFunc('categories','admin', 'create',
                                 array('name'        => xarML("Images"),
                                       'description'   => xarML("Image files (png, jpg, gif, bmp, etc)"),
                                       'parent_id'     => $publicDir));

    $docsDir    = xarModAPIFunc('categories','admin', 'create',
                                 array('name'        => xarML("Documents"),
                                       'description'   => xarML("Documents (pdf, txt, doc, etc)"),
                                       'parent_id'     => $publicDir));

    $trustDir   = xarModAPIFunc('categories','admin', 'create',
                                 array('name'        => xarML("Trusted Mount"),
                                       'description'   => xarML("Trusted Mount point - think about changing the name for this..."),
                                       'parent_id'     => $publicDir));

    // Add default home directory (for anonymous, etc)
    xarModSetVar('uploads', 'folders.home', $publicDir);

    // Move all the old modvars to their new names
    $trustPath  = xarModGetVar('uploads', 'path.imports-directory');
    $untrustDir = xarModGetVar('uploads', 'path.uploads-directory');
    $cwd        = xarModGetVar('uploads', 'path.imports-cwd');

    $mountpoints[$trustDir]          = $trustPath;
    $mountopts[$trustDir]['exclude'] = '^.*(/SCCS/|/\.).*$';
    xarModSetVar('uploads', 'mountpoints', serialize($mountpoints));
    xarModSetVar('uploads', 'mountopts',   serialize($mountopts));

    $mountinfo[$trustDir]['path']           = $trustPath;
    $mountinfo[$trustDir]['filter.dir']     = '^.*(/SCCS/|/\.).*$';
    $mountinfo[$trustDir]['filter.file']    = '^.*\/\..*$';
    xarModSetVar('uploads', 'mount.list', serialize($mountinfo));


    xarModDelVar('uploads', 'path.imports-directory');
    xarModDelVar('uploads', 'path.cwd');
    xarModDelVar('uploads', 'path.uploads-directory');

    xarModSetVar('uploads', 'path.untrust', $untrustDir);

    // Add blocksize module var (hardcoded to 64K for now)
    xarModSetVar('uploads', 'db.blocksize', (64 * 1024));

    $fileList = xarModAPIFUnc('uploads', 'user', 'db_getall_files');

    // It seems that their is a high possibility for duplicate file entries. So, we'll keep them for now
    // and work out how to remove them later on (Administrator utility or something like that...)
    $locationCache = array();

    foreach ($fileList as $file) {

        // Figure out which vdir each file will be associated with
        if (stristr($file['fileLocation'], $trustPath)) {
            $where = $trustDir;
        } elseif (stristr($file['mimetype']['text'], 'image')) {
            $where = $imagesDir;
        } elseif (eregi('(pdf|doc|text|word)', $file['mimetype']['text'])) {
            $where = $docsDir;
        } else {
            $where = $publicDir;
        }

        if (stristr($file['fileLocation'], $trustPath)) {

            // Mounted files need to have their path translated to:
            // mount://[mount point id]/[relative path to file]
            $path = str_replace($trustPath, '', $file['fileLocation']);

            if ($path{0} == '/') {
                $path = substr($path, 1);
            }

            $location = "mount://$where/$path";

        } elseif (stristr($file['fileLocation'], $untrustDir)) {

            $location = str_replace($untrustDir, "xarfs://$where", $file['fileLocation']);

        } else {
            // Move hardcoded files to untrust while maintaining
            // their curent  file id and metadata in the inode
            // table but change the location field

            if (!in_array($file['fileLocation'], array_keys($locationCache))) {
                $newName = xarModAPIFunc('uploads', 'fs', 'obfuscate_name',
                                        array('name' => $file['fileName']));

                // Make sure we have a unique name..
                while (file_exists($untrustDir . '/' . $newName)) {
                    $newName = xarModAPIFunc('uploads', 'fs', 'obfuscate_name',
                                            array('name' => $file['fileName']));
                }

                // Go ahead and move the file to the untrust directory
                // and remember whether or not it succeeded
                $result = xarModAPIFunc('uploads', 'fs', 'move',
                    array(
                        'source'        => $file['fileLocation'],
                        'destination'   => $untrustDir . '/' . $newName
                    )
                );
                
                if (!$result) {
                    // If for some strange reason we can't move it
                    // to the untrust directory, then let it remain
                    // where it is, and keep it as a hardcoded file
                    $location = 'file://' . $file['fileLocation'];
                } else {
                    $location = "xarfs://$where/" . "$newName";
                }

                $locationCache[$file['fileLocation']] = $location;
            } else {
                $location = $locationCache[$file['fileLocation']];
            }
        }

        xarModAPIFunc('uploads', 'user', 'db_modify_file',
                        array('fileId' => $file['fileId'],
                              'fileLocation' => $location));

        xarModAPIFunc('uploads', 'user', 'db_add_association',
                       array('fileid'   => $file['id'],
                             'modid'    => xarModGetIDFromName('categories'),
                             'itemid'   => $where));

    }

    /* Setup default instance values */
    $instances[0]['header'] = 'external';
    $instances[0]['limit']  = 0;

    /* Setup File specific mask attributes */
    xarRegisterMask('File Read',   'All', 'uploads', 'File', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('File Modify', 'All', 'uploads', 'File', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('File Write',  'All', 'uploads', 'File', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('File Delete', 'All', 'uploads', 'File', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('File Full Control',   'All', 'uploads', 'File', 'All:All:All', 'ACCESS_ADMIN');
    $instances[0]['query']  = xarModURL('uploads', 'admin', 'privileges', array('mask' => 'File'));
    xarDefineInstance('uploads', 'File', $instances);

    /* Setup Directory specific mask attributes */
    xarRegisterMask('Directory List',   'All', 'uploads', 'Directory', 'All', 'ACCESS_READ');
    xarRegisterMask('Directory Modify', 'All', 'uploads', 'Directory', 'All', 'ACCESS_EDIT');
    xarRegisterMask('Directory Create', 'All', 'uploads', 'Directory', 'All', 'ACCESS_ADD');
    xarRegisterMask('Directory Delete', 'All', 'uploads', 'Directory', 'All', 'ACCESS_DELETE');
    xarRegisterMask('Directory Full Control',   'All', 'uploads', 'Directory', 'All', 'ACCESS_ADMIN');

    $instances[0]['query']  = xarModURL('uploads', 'admin', 'privileges', array('mask' => 'Directory'));
    xarDefineInstance('uploads', 'Directory', $instances);

    /* Setup FileUpload specific mask attributes */
    xarRegisterMask('File Upload',     'All', 'uploads', 'FileUpload', 'All:All', 'ACCESS_ADD');

    $instances[0]['query']  = xarModURL('uploads', 'admin', 'privileges', array('mask' => 'FileUpload'));
    xarDefineInstance('uploads', 'FileUpload', $instances);

    /* Setup File Import specific mask attributes */
    xarRegisterMask('File Import',     'All', 'uploads', 'FileUpload', 'All:All', 'ACCESS_ADD');

    $instances[0]['query']  = xarModURL('uploads', 'admin', 'privileges', array('mask' => 'FileUpload'));
    xarDefineInstance('uploads', 'FileUpload', $instances);

    /* Setup FileDownload specific mask attributes */
    xarRegisterMask('File Download', 'All', 'uploads', 'FileDownload', 'All:All:All', 'ACCESS_READ');

    $instances[0]['query']  = xarModURL('uploads', 'admin', 'privileges', array('mask' => 'FileDownload'));
    xarDefineInstance('uploads', 'FileDownload', $instances);

    if (!xarModRegisterHook('module', 'modifyconfig', 'GUI', 'uploads', 'admin', 'modifyconfighook')) {
         $msg = xarML('Could not register hook:item - modify - GUI');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }

    if (!xarModRegisterHook('module', 'updateconfig', 'API', 'uploads', 'admin', 'updateconfighook')) {
         $msg = xarML('Could not register hook: item - update - API');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }

    if (!xarModRegisterHook('item', 'new', 'GUI', 'uploads', 'admin', 'newhook')) {
         $msg = xarML('Could not register hook: item - create - API');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }

    if (!xarModRegisterHook('item', 'create', 'API', 'uploads', 'admin', 'createhook')) {
         $msg = xarML('Could not register hook: item - update - API');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }

    if (!xarModRegisterHook('item', 'modify', 'GUI', 'uploads', 'admin', 'modifyhook')) {
         $msg = xarML('Could not register hook: item - create - API');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }

    if (!xarModRegisterHook('item', 'update', 'API', 'uploads', 'admin', 'updatehook')) {
         $msg = xarML('Could not register hook: item - update - API');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }

    if (!xarModRegisterHook('item', 'display', 'GUI', 'uploads', 'user', 'displayhook')) {
         $msg = xarML('Could not register hook: item - create - API');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
         return;
    }
?>