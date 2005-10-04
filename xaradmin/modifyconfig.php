<?php

function filemanager_admin_modifyconfig()
{

    xarModAPILoad('filemanager', 'user');

    // Security check
    if (!xarSecurityCheck('AdminFileManager')) return;

    // Generate a one-time authorisation code for this operation

    // get the current module variables for display
    // *********************************************
    // Global
    $data['path']['filemanager-directory']      = xarModGetVar('filemanager', 'path.filemanager-directory');
    $data['path']['imports-directory']      = xarModGetVar('filemanager', 'path.imports-directory');
    $data['file']['maxsize']                = number_format(xarModGetVar('filemanager', 'file.maxsize'));
    $data['file']['delete-confirmation']    = xarModGetVar('filemanager', 'file.delete-confirmation');
    $data['file']['auto-purge']             = xarModGetVar('filemanager', 'file.auto-purge');
    $data['file']['auto-approve']           = xarModGetVar('filemanager', 'file.auto-approve');
    $data['file']['obfuscate-on-import']    = xarModGetVar('filemanager', 'file.obfuscate-on-import');
    $data['file']['obfuscate-on-upload']    = xarModGetVar('filemanager', 'file.obfuscate-on-upload');
    $data['ddprop']['trusted']              = xarModGetVar('filemanager', 'dd.fileupload.trusted');
    $data['ddprop']['external']             = xarModGetVar('filemanager', 'dd.fileupload.external');
    $data['ddprop']['stored']               = xarModGetVar('filemanager', 'dd.fileupload.stored');
    $data['ddprop']['upload']               = xarModGetVar('filemanager', 'dd.fileupload.upload');
    $data['authid']                         = xarSecGenAuthKey();

    $data['approveList']['noone']      = _FILEMANAGER_APPROVE_NOONE;
    $data['approveList']['admin']      = _FILEMANAGER_APPROVE_ADMIN;
    $data['approveList']['everyone']   = _FILEMANAGER_APPROVE_EVERYONE;

    if ($data['file']['auto-approve'] != _FILEMANAGER_APPROVE_NOONE &&
        $data['file']['auto-approve'] != _FILEMANAGER_APPROVE_ADMIN &&
        $data['file']['auto-approve'] != _FILEMANAGER_APPROVE_EVERYONE) {
            $data['file']['auto-approve'] = _FILEMANAGER_APPROVE_NOONE;
    }

    $hooks = xarModCallHooks('module', 'modifyconfig', 'filemanager',
                             array('module'   => 'filemanager',
                                   'itemtype' => 1)); // Files

    if (empty($hooks)) {
        $data['hooks'] = '';
    } else {
        $data['hooks'] = $hooks;
    }

    // fetch the tab their looking at.  we'll use this to figure which template to include as well
    xarVarFetch('tab', 'str:1:100', $data['tab'], 'overview', XARVAR_NOT_REQUIRED);

    // create an array of all the tabs we want have the template show
    $data['tabsarray'] = array('overview'=> xarML('Overveiw'),
                               'stats'   => xarML('Stats'),
                               'browse'  => xarML('Browse'),
                               'addfiles'=> xarML('Add Files'),
                               'mount'   => xarML('Mount'),
                               'pending' => xarML('Pending'),
                               'settings'=> xarML('Settings'),
                               'help'    => xarML('Help'));

    // Return the template variables defined in this function
    return $data;
}
?>
