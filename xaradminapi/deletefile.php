<?php

function downloads_adminapi_deletefile($args) {

	extract($args);
	 
	chdir($location);
	$delete = unlink($file);
	if ($delete) return true;
	else return false;

}

?>