<?php

/** 
 *  Processes incoming files (uploades / imports)
 *
 *  @author  Carl P. Corliss (aka Rabbitt)
 *  @access  public
 *  @param   string     importFrom  The complete path to a (local) directory to import files from
 *  @param   array      override    Array containing override values for import/uplaod path/obfuscate
 *  @param   string     override.upload.path        Override the upload path with the specified value
 *  @param   string     override.upload.obfuscate   Override the upload filename obfuscation 
 *  @param   string     override.import.path        Override the import path with the specified value
 *  @param   string     override.import.obfuscate   Override the import filename obfuscation 
 *  @returns array      list of files the files that were requested to be stored. If they had errors,
 *                      they will have 'error' index defined and will -not- have been added. otherwise,
 *                      they will have a fileId associated with them if they were added to the DB
 */
 
xarModAPILoad('uploads', 'user');
 
function uploads_userapi_process_files( $args ) {

    extract($args);

    $storeList = array();
    
    // If we have an import then verify the information given
    if (!isset($importFrom) || empty($importFrom)) {
        $importFrom = NULL;
        $override['import']['path'] = NULL;
    } else {
        if (!empty($override['import']['path']) && file_exists($import_path_override)) {
            $import_directory = $override['import']['path'];
        } else {
            $import_directory = xarModGetVar('uploads','path.imports-directory');
        }
    }
    
    // if there is an override['upload']['path'], use that
    if (isset($override['upload']['path']) && file_exists($override['upload']['path'])) {
        $upload_directory = $override['upload']['path'];
    } else {
        $upload_directory = xarModGetVar('uploads','path.uploads-directotry');
    }
    
    // Check for override of upload obfuscation and set accordingly
    if (isset($override['upload']['obfuscate']) && $override['upload']['obfuscate']) {
        $upload_obfuscate = TRUE;
    } else {
        $upload_obfuscate = FALSE;
    }
    
    // Check for override of import obfuscation and set accordingly
    if (isset($override['import']['obfuscate']) && $override['import']['obfuscate']) {
        $import_obfuscate = TRUE;
    } else {
        $import_obfuscate = FALSE;
    }
    
    // If not store type defined, default to DB ENTRY AND FILESYSTEM STORE
    if (!isset($store_type)) {
        // this is the same as _UPLOADS_STORE_DB_ENTRY OR'd with _UPLOADS_STORE_FILESYSTEM
        $store_type = _UPLOADS_STORE_FSDB;
    }
    
    /**
     * Prepare the uploaded file list
     */

     
    $fileList = xarModAPIFunc('uploads','user','prepare_uploads', 
                               array('savePath'  => $upload_directory,
                                     'obfuscate' => $upload_obfuscate));
    
    //   TESTING LINE BELOW.
    //   echo "<br /><pre> fileList => "; print_r($fileList); echo "</pre>"; 
    /**
     * Prepare the imported files file list
     */    
    if (isset($importFrom)) {
        $args = array('importFrom'  => $importFrom,
                      'savePath'    => $import_directory, 
                      'obfuscate'   => $import_obfuscate);
        
        if (!empty($fileList)) {
            $fileList = array_merge($fileList, xarModAPIFunc('uploads', 'user', 'prepare_imports', $args));
        } else {
            $fileList = xarModAPIFunc('uploads', 'user', 'prepare_imports',$args);
        }
    } 
    
    /**
     *  Iterate through each file in the list and store it, providing it doesn't have errors defined.
     */

    foreach ($fileList as $fileInfo) {
        
        // If the file has errors, add the file to the storeList (with it's errors intact),
        // and continue to the next file in the list. Note: it's up to the calling function 
        // to deal with the error (or not) - however, we won't be adding the file with errors :-)
        if (isset($fileInfo['errors'])) {
            $storeList[] = $fileInfo;
            continue;
        }
        $storeList[] = xarModAPIFunc('uploads', 'user', 'file_store',
                                      array('fileInfo'  => $fileInfo,
                                            'storeType' => $store_type));
    }
    
    return $storeList;
}

?>
