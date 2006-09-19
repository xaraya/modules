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
function xproject_team_update($args)
{
    extract($args);

    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('projectrole', 'str::', $projectrole, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid', 'id', $memberid)) return;

    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('xproject',
                    'team',
                    'update',
                    array('projectid'        => $projectid,
                        'projectrole'        => $projectrole,
                        'memberid'            => $memberid))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Team Member Updated'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'display', array('projectid' => $projectid)));

    return true;
}

?>