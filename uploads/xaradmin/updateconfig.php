<?php
function uploads_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('maximum_upload_size', 'int:1:', $maximum_upload_size, 100000, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_image_width', 'int:1:', $max_image_width, 600, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_image_height', 'int:1:', $max_image_height, 800, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowed_types', 'str:1:', $allowed_types, 'gif;jpg;zip;txt', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uploads_directory', 'str:1:', $uploads_directory, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm_delete', 'str:1:', $confirm_delete, '1', XARVAR_NOT_REQUIRED)) return;



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

    // Confirm authorisation code.  
    if (!xarSecConfirmAuthKey()) return;

    xarModSetVar('uploads', 'uploads_directory', $uploads_directory);
    xarModSetVar('uploads', 'maximum_upload_size', $maximum_upload_size);
    xarModSetVar('uploads', 'max_image_height', $max_image_height);
    xarModSetVar('uploads', 'max_image_width', $max_image_width);
    xarModSetVar('uploads', 'allowed_types', $allowed_types);
    xarModSetVar('uploads', 'confirm_delete', $confirm_delete);


    xarModCallHooks('module',
                    'updateconfig',
                    'uploads',
                    array('module' => 'uploads'));

    xarResponseRedirect(xarModURL('uploads', 'admin', 'modifyconfig'));

    // Return
    return true;
}
?>