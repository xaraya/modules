<?php
/**
 * XProject Module - A project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function accessmethods_admin_delete($args)
{
    if (!xarVarFetch('siteid', 'id', $siteid, $siteid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str::', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

     if (!empty($objectid)) {
         $siteid = $objectid;
     }
    $item = xarModAPIFunc('accessmethods',
                         'user',
                         'get',
                         array('siteid' => $siteid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    if (!xarSecurityCheck('DeleteAccessMethods', 1, 'All', "$item[webmasterid]")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'accessmethods', xarVarPrepForDisplay($siteid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (empty($confirm)) {
    
        $data = xarModAPIFunc('accessmethods','admin','menu');

        $data['siteid'] = $siteid;

        $data['site_name'] = xarVarPrepForDisplay($item['site_name']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('accessmethods',
                     'admin',
                     'delete',
                     array('siteid' => $siteid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Access Method Deleted'));

    xarResponseRedirect(xarModURL('accessmethods', 'admin', 'view'));

    return true;
}

?>
