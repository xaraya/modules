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
/**
 * insertuser - insert a user into a group
 */
function xproject_groups_insertuser($args)
{
    extract($args);
    if (!xarVarFetch('gid',   'id', $gid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uid',   'id', $uid,   0, XARVAR_NOT_REQUIRED)) return;
    // Security check
    if (!xarSecurityCheck('AddXProject', 0, 'Group', "All:All:All"))

    if (!xarSecConfirmAuthKey()) {
        return;
    }

    if (xarModAPIFunc('xproject',
                    'groups',
                    'insertuser', array('gid' => $gid,
                                        'uid' => $uid))) {

        xarSessionSetVar('statusmsg', xarML('User Added'));
    }

    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
}

?>