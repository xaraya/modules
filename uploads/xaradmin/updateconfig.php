<?php

function uploads_admin_updateconfig()
{
    // Get parameters

    if (!xarVarFetch('uploads_directory', 'str:1:', $uploads_directory, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maximum_upload_size', 'int:1:', $maximum_upload_size, 100000, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowed_types', 'str:1:', $allowed_types, 'gif;jpg;zip;txt', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm_delete', 'str:1:', $confirm_delete, '1', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('max_image_width', 'int:1:', $max_image_width, 600, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_image_height', 'int:1:', $max_image_height, 800, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('thumbnail_setting', 'str:1:', $thumbnail_setting, '0', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('netpbm_path', 'str:1:', $netpbm_path, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('import_directory', 'str:1:', $import_directory, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('obfuscate_imports', 'str:1:', $obfuscate_imports, '0', XARVAR_NOT_REQUIRED)) return;



    // Confirm authorisation code.  
    if (!xarSecConfirmAuthKey()) return;

    xarModSetVar('uploads', 'uploads_directory', $uploads_directory);
    xarModSetVar('uploads', 'allowed_types', $allowed_types);
    xarModSetVar('uploads', 'confirm_delete', $confirm_delete);
    xarModSetVar('uploads', 'maximum_upload_size', $maximum_upload_size);

    xarModSetVar('uploads', 'max_image_height', $max_image_height);
    xarModSetVar('uploads', 'max_image_width', $max_image_width);
	xarModSetVar('uploads', 'thumbnail_setting', $thumbnail_setting);
	xarModSetVar('uploads', 'netpbm_path', $netpbm_path);

    xarModSetVar('uploads', 'import_directory',  $import_directory);
    xarModSetVar('uploads', 'obfuscate_imports', $obfuscate_imports);

    xarModCallHooks('module',
                    'updateconfig',
                    'uploads',
                    array('module' => 'uploads'));

    xarResponseRedirect(xarModURL('uploads', 'admin', 'modifyconfig'));

    // Return
    return true;
}
?>