<?php

function uploads_admin_modifyconfig()
{

    // Security check 
    if (!xarSecurityCheck('AdminUploads')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid']                 = xarSecGenAuthKey();

    // get the current module variables for display
	// *********************************************
	// Global
    $data['uploads_directory']      = xarModGetVar('uploads', 'uploads_directory');
    $data['maximum_upload_size']    = xarModGetVar('uploads', 'maximum_upload_size');
    $data['allowed_types']          = xarModGetVar('uploads', 'allowed_types');
    $data['confirm_delete']          = xarModGetVar('uploads', 'confirm_delete');

	// Import
    $data['import_directory']       = xarModGetVar('uploads', 'import_directory');
    $data['obfuscate_imports']      = xarModGetVar('uploads', 'obfuscate_imports');

	// Images
    $data['max_image_width']        = xarModGetVar('uploads', 'max_image_width');
    $data['max_image_height']       = xarModGetVar('uploads', 'max_image_height');
	$data['thumbnail_setting']      = xarModGetVar('uploads', 'thumbnail_setting');
	$data['netpbm_path']      		= xarModGetVar('uploads', 'netpbm_path');
	$data['thumbnail_path']      	= xarModGetVar('uploads', 'thumbnail_path');


	// Setup Defaults
	// **************
    if (empty($uploads_directory))
	{
		if( isset( $_SERVER['PATH_TRANSLATED'] ) )
		{
        	$uploads_directory = dirname(realpath($_SERVER['PATH_TRANSLATED'])) . '/var/uploads/';
		} elseif( isset( $_SERVER['SCRIPT_FILENAME'] ) ) {
        	$uploads_directory = dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/var/uploads/';
		} else {
        	$uploads_directory = 'var/uploads/';
		}
    }



    $hooks = xarModCallHooks('module',
                             'modifyconfig',
                             'uploads',
                             array('uploads' => 'example'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // Return the template variables defined in this function
    return $data;
}
?>