<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_admin_chngimportance($args)
{
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('mode', 'str:1:', $mode, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('xproject',
                    'admin',
                    'chngimportance',
                    array('projectid'        => $projectid,
                        'mode'              => $mode))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Project Importance Changed'));

    if(!empty($returnurl)) {
        xarResponseRedirect($returnurl);
        return true;
    }

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));

    return true;
}

?>