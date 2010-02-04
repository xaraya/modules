<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author Andrea Moro
 */
/**
 * the main administration function
 *
 * @author Andrea Moro
 * @access public
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function sharecontent_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminSharecontent')) return;

    $refererinfo = xarRequest::getInfo(xarServer::getVar('HTTP_REFERER'));
    $info = xarRequest::getInfo();
    $samemodule = $info[0] == $refererinfo[0];

    if (((bool)xarModVars::get('modules', 'disableoverview') == false) || $samemodule){
        return xarTplModule('sharecontent','admin','main');
    } else {
        xarResponse::redirect(xarModURL('sharecontent', 'admin', 'modifyconfig'));
        return true;
    }
}
?>