<?php
/**
 * XProject Module - A simple project management module
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
function xproject_groups_addgroup()
{
    if (!xarVarFetch('gname',   'str::', $gname,   '', XARVAR_NOT_REQUIRED)) return;
    // Security check
    if (!xarSecurityCheck('AddXProject', 0, 'Group', "All:All:All"))
    if (!xarSecConfirmAuthKey()) {
        return;
    }

    $gname = xarModAPIFunc('xproject',
              'groups',
              'addgroup', array('gname' => $gname));

    if ($gname == false) {
        return $gname;
    }

    xarSessionSetVar('statusmsg', xarML('Group added'));
    // Return to?
    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
}
?>