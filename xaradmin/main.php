<?php
/**
 * Messages Module
 *
 * @package modules
 * @subpackage messages module
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 * @author Ryan Walker
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function messages_admin_main()
{
    if (!xarSecurityCheck('AdminMessages')) {
        return;
    }

    $refererinfo =  xarController::$request->getInfo(xarServer::getVar('HTTP_REFERER'));
    $info =  xarController::$request->getInfo();
    $samemodule = $info[0] == $refererinfo[0];
    
    if (((bool)xarModVars::get('modules', 'disableoverview') == false) || $samemodule) {
        if (!xarVarFetch('tab', 'str', $data['tab'], '', XARVAR_NOT_REQUIRED)) {
            return;
        }
        return xarTplModule('messages', 'admin', 'overview', $data);
    } else {
        xarResponse::redirect(xarModURL('messages', 'admin', 'modifyconfig'));
        return true;
    }
}
