<?php

/**
 *  Retrieves metadata on a file from the filesystem
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string   fileLocation  The location of the file on in the filesystem
 *  @param   boolean  normalize     Whether or not to 
 *  @returns array                  array containing the inodeType, fileSize, fileType, fileLocation, fileName
 *
 */
 
function uploads_userapi_file_get_metadata( $args ) {
 
    extract($args);

    if (!isset($normalize)) {
        $normalize = FALSE;
    }
        
    if (isset($fileLocation) && !empty($fileLocation) && file_exists($fileLocation)) {
        
        $file =& $fileLocation;
        if (is_dir($file)) {
            $type = _INODE_TYPE_DIRECTORY;
            $size = 'N/A';
            $mime = 'filesystem/directory';
        } elseif (is_file($file)) {
            $type = _INODE_TYPE_FILE;
            $size = filesize($file);
            $mime = xarModAPIFunc('mime', 'user', 'analyze_file', array('fileName' => $file));
        } else {
            $type = _INODE_TYPE_UNKNOWN;
            $size = 0;
            $mime = 'application/octet';
        }

        $name = basename($file);    

        if ($normalize) {
            $size = xarModAPIFunc('uploads', 'user', 'normalize_filesize', $size);
        }

		$relative_path = str_replace(xarModGetVar('uploads', 'path.imports-directory'), '/trusted', $file);
        
        $fileInfo = array('inodeType'    => $type,
                          'fileName'     => $name,
                          'fileLocation' => $file,
						  'relativePath' => $relative_path,
                          'fileType'     => $mime,
                          'fileSize'     => $size);

        return $fileInfo;
    } else {
        // TODO: exception
        return FALSE;
    }
}
 
?>
