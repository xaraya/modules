<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 *  Purges all files with REJECTED status from the system
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   boolean    confirmation    whether or not to skip confirmation
 *  @param   string     authid          the authentication id
 *  @return  void
 *
 */

xarMod::apiLoad('uploads', 'user');

function uploads_admin_purge_rejected($args)
{
    extract($args);

    if (!xarSecurity::check('ManageUploads')) {
        return;
    }

    if (isset($authid)) {
        $_GET['authid'] = $authid;
    }

    if (!isset($confirmation)) {
        xarVar::fetch('confirmation', 'int:1:', $confirmation, '', xarVar::NOT_REQUIRED);
    }
    // Confirm authorisation code.
    if (!xarSec::confirmAuthKey()) {
        return;
    }


    if ((isset($confirmation) && $confirmation) || !xarModVars::get('uploads', 'file.delete-confirmation')) {
        $fileList = xarMod::apiFunc(
            'uploads',
            'user',
            'db_get_file',
            array('fileStatus' => _UPLOADS_STATUS_REJECTED)
        );

        if (empty($fileList)) {
            xarController::redirect(xarController::URL('uploads', 'admin', 'view'));
            return;
        } else {
            $result = xarMod::apiFunc('uploads', 'user', 'purge_files', array('fileList' => $fileList));
            if (!$result) {
                $msg = xarML('Unable to purge rejected files!');
                throw new Exception($msg);
            }
        }
    } else {
        $fileList = xarMod::apiFunc(
            'uploads',
            'user',
            'db_get_file',
            array('fileStatus' => _UPLOADS_STATUS_REJECTED)
        );
        if (empty($fileList)) {
            $data['fileList']   = array();
        } else {
            $data['fileList']   = $fileList;
        }
        $data['authid']     = xarSec::genAuthKey();

        return $data;
    }

    xarController::redirect(xarController::URL('uploads', 'admin', 'view'));
}
