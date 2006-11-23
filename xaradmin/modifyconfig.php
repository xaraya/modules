<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
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
    $data['file']['cache-expire']           = xarModGetVar('uploads', 'file.cache-expire');
    if (!isset($data['file']['cache-expire'])) {
        xarModSetVar('uploads', 'file.cache-expire', 0);
    }
    $data['file']['allow-duplicate-upload'] = xarModGetVar('uploads', 'file.allow-duplicate-upload');
    if (!isset($data['file']['allow-duplicate-upload'])) {
        xarModSetVar('uploads', 'file.allow-duplicate-upload', 0);
        $data['file']['allow-duplicate-upload'] = 0;
    }
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

    $hooks = xarModCallHooks('module', 'modifyconfig', 'uploads',
                             array('module'   => 'uploads',
                                   'itemtype' => 1)); // Files

    if (empty($hooks)) {
        $data['hooks'] = '';
    } else {
        $data['hooks'] = $hooks;
    }
    // Return the template variables defined in this function
    return $data;
}
?>
