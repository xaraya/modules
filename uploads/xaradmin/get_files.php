<?php

// load defined constants
xarModAPILoad('uploads', 'user');

function uploads_admin_get_files() {
    
    if (!xarSecurityCheck('AddUploads')) return;

	$actionList[] = _UPLOADS_GET_UPLOAD;
	$actionList[] = _UPLOADS_GET_EXTERNAL;
	$actionList[] = _UPLOADS_GET_LOCAL;
	$actionList[] = _UPLOADS_GET_REFRESH_LOCAL;
	$actionList = 'enum:' . implode(':', $actionList);

	if (!xarVarFetch('action',        $actionList, $action, '', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('fileList',      'list:str:1:', $fileList, '', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('file_all',      'checkbox', $file_all, '', XARVAR_NOT_REQUIRED)) return;
	
	// StoreType can -only- be one of FSDB or DB_FULL
	$storeTypes = _UPLOADS_STORE_FSDB . ':' . _UPLOADS_STORE_DB_FULL;
	if (!xarVarFetch('storeType',     "enum:$storeTypes", $storeType, '', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('upload',        'array:1:', $upload, '', XARVAR_NOT_REQUIRED)) return;

	// minimum external import link must be: ftp://a.ws  <-- 10 characters total
	if (!xarVarFetch('import',        'str:10:', $import, '', XARVAR_NOT_REQUIRED)) return;
	
	// now make sure someone hasn't tried to change our maxsize on us ;-)
	$file_maxsize = xarModGetVar('uploads', 'file.maxsize');
	if (!xarVarFetch('MAX_FILE_SIZE', "int::$file_maxsize", $maxsize, '', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('inode', 'regexp:/(?<!\.{2,2}\/)[\w\d]*/', $inode, '', XARVAR_NOT_REQUIRED)) return;

    if (!isset($action)) {
		$action = NULL;
	}

	switch ($action) {
		case _UPLOADS_GET_UPLOAD:
		case _UPLOADS_GET_EXTERNAL:
		    $list = xarModAPIFunc('uploads','user','process_files',
		                           array('importFrom' => $import));

		    if (is_array($list) && count($list)) {
		        return xarTplModule('uploads','admin', 'addfile-status', array('fileList' => $list), NULL);
		    } else {
		        xarResponseRedirect(xarModURL('uploads', 'admin', 'get_files'));				return;
		    }
			break;
		case _UPLOADS_GET_LOCAL:
			break;
		case _UPLOADS_GET_REFRESH_LOCAL:

            $cwd = xarModAPIFunc('uploads', 'user', 'import_chdir', array('dirName' => isset($inode) ? $inode : NULL));

			$data['fileList'] = xarModAPIFunc('uploads', 'user', 'import_get_filelist', 
									   array('fileLocation' => $cwd));

			$data['curDir'] = str_replace(xarModGetVar('uploads', 'path.imports-directory'), '', $cwd);
			foreach ($data['fileList'] as $file => $fileInfo) {
				if ($fileInfo[0] == _INODE_TYPE_FILE) {
					$data['fileList'][$file]['fileSize'] = xarModAPIFunc('uploads', 'user', 'normalize_filesize', 
																  array('fileSize' => $fileInfo['fileSize']));
				}
			}
		default:
			// reset the CWD for the local import
			// then only display the: 'check for new imports' button
    		$data['authid'] = xarSecGenAuthKey();
		    $data['file_maxsize'] = $file_maxsize;
			break;
	}
    $data['local_import_post_url'] = xarModURL('uploads', 'admin', 'get_files');
    $data['external_import_post_url'] = xarModURL('uploads', 'admin', 'get_files');
    $data['upload_post_url'] = xarModURL('uploads', 'admin', 'get_files');
    // Generate a one-time authorisation code for this operation
    return $data;
}
?>
