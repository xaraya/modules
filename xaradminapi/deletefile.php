<?php

function downloads_adminapi_deletefile($args) {

	extract($args);

	if (!xarSecurityCheck('DeleteDownloads',0)) return;
	 
	chdir($location);
	$delete = unlink($file);
	if ($delete) return true;
	else return false;

}

?>