<?php

function uploads_admin_modifyconfig()
{

    xarModAPILoad('uploads', 'user');
    
    // Security check 
    if (!xarSecurityCheck('AdminUploads')) return;

    // Generate a one-time authorisation code for this operation

    // get the current module variables for display
    // *********************************************
    // Global
    $data['path']['uploads-directory']      = xarModGetVar('uploads', 'path.uploads-directory');
    $data['path']['imports-directory']      = xarModGetVar('uploads', 'path.imports-directory');
    $data['file']['maxsize']                = number_format(xarModGetVar('uploads', 'file.maxsize'));
    $data['file']['delete-confirmation']    = xarModGetVar('uploads', 'file.delete-confirmation');
    $data['file']['auto-purge']             = xarModGetVar('uploads', 'file.auto-purge');
    $data['file']['auto-approve']           = xarModGetVar('uploads', 'file.auto-approve');
    $data['file']['obfuscate-on-import']    = xarModGetVar('uploads', 'file.obfuscate-on-import');
    $data['file']['obfuscate-on-upload']    = xarModGetVar('uploads', 'file.obfuscate-on-upload');
    $data['ddprop']['trusted']              = xarModGetVar('uploads', 'dd.fileupload.trusted');
    $data['ddprop']['external']             = xarModGetVar('uploads', 'dd.fileupload.external');
    $data['ddprop']['stored']               = xarModGetVar('uploads', 'dd.fileupload.stored');
    $data['ddprop']['upload']               = xarModGetVar('uploads', 'dd.fileupload.upload');
    $data['authid']                         = xarSecGenAuthKey();
    
    $data['approveList']['noone']      = _UPLOADS_APPROVE_NOONE;
    $data['approveList']['admin']      = _UPLOADS_APPROVE_ADMIN;
    $data['approveList']['everyone']   = _UPLOADS_APPROVE_EVERYONE;
    
    if ($data['file']['auto-approve'] != _UPLOADS_APPROVE_NOONE && 
        $data['file']['auto-approve'] != _UPLOADS_APPROVE_ADMIN &&
        $data['file']['auto-approve'] != _UPLOADS_APPROVE_EVERYONE) {
            $data['file']['auto-approve'] = _UPLOADS_APPROVE_NOONE;
    }
    
    $hooks = xarModCallHooks('module', 'modifyconfig', 'uploads', array('uploads' => 'example'));
    
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