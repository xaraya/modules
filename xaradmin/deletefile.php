<?php

function downloads_admin_deletefile() {

	if(!xarVarFetch('file',       'str',    $file,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('directory',       'str',    $directory,   NULL, XARVAR_DONT_SET)) {return;}
	if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

	if (strstr($file,'.')) {
		$parts = explode('.',$file);
		$ext = strtolower(end($parts));
	} else {
		$ext = '';
	}

	$instance = $ext; 
	if (!xarSecurityCheck('DeleteDownloadsFiles',0,'File',$instance)) {
		return;
	}

	$data['file'] = $file;
	$data['directory'] = $directory;
	 
	if ($data['confirm']) {

		$location = xarMod::apiFunc('downloads','admin','getbasepath') . $directory;
		$delete = xarMod::apiFunc('downloads','admin','deletefile', array(
			'location' => $location,
			'file' => $file
			));

		if ($delete) {
			xarResponse::redirect(xarModURL('downloads', 'admin', 'files'));
		} else {
			$data['error'] = 'some problem';
			return xarTplModule('downloads','admin','deletefile', $data); 
		}

	} else {
		return xarTplModule('downloads','admin','deletefile', $data); 
	}

}

?>