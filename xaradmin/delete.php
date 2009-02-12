<?php
/**
 * Dossier Module - A project management module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author Chad Kraeft
 */
function dossier_admin_delete($args)
{
    if (!xarVarFetch('contactid', 'id', $contactid, $contactid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str::', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if (!empty($objectid)) {
        $contactid = $objectid;
    }
    $item = xarModAPIFunc('dossier',
                         'user',
                         'get',
                         array('contactid' => $contactid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $uid = xarSessionGetVar('uid');
    
    if ($item['userid'] != $uid) {
        if (!xarSecurityCheck('AuditDossierLog', 1, 'Log', "All:All:All:All")) {
            $msg = xarML('Not authorized to delete #(1) item #(2)',
                        'dossier', xarVarPrepForDisplay($siteid));
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                           new SystemException($msg));
            return;
        }
    }
    
    if(empty($returnurl)) $returnurl = xarModURL('dossier', 'admin', 'view');

    if (empty($confirm)) {
    
        $data = xarModAPIFunc('dossier','admin','menu');

        $data['contactid'] = $contactid;

        $data['returnurl'] = $returnurl;

        $data['sortname'] = xarVarPrepForDisplay($item['sortname']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('dossier',
                     'admin',
                     'delete',
                     array('contactid' => $contactid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Contact Deleted'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
