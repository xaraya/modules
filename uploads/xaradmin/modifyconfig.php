<?php
function uploads_admin_modifyconfig()
{

    // Security check 
    if (!xarSecurityCheck('AdminUploads')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid']                 = xarSecGenAuthKey();

    // get the current module variables for display
    $data['uploads_directory']      = xarModGetVar('uploads', 'uploads_directory');
    $data['maximum_upload_size']    = xarModGetVar('uploads','maximum_upload_size');
    $data['max_image_width']        = xarModGetVar('uploads','max_image_width');
    $data['max_image_height']       = xarModGetVar('uploads','max_image_height');
    $data['allowed_types']          = xarModGetVar('uploads', 'allowed_types');
    $data['confirm_delete']          = xarModGetVar('uploads', 'confirm_delete');

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