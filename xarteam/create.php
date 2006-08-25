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
function xproject_team_create($args)
{
    extract($args);
    
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('memberid', 'id', $memberid)) return;
    if (!xarVarFetch('projectrole', 'str::', $projectrole, '', XARVAR_NOT_REQUIRED)) return;
    
    if(!xarModLoad('addressbook', 'user')) return;
    
    if (!xarSecConfirmAuthKey()) return;

    $featureid = xarModAPIFunc('xproject',
                        'team',
                        'create',
                        array('projectid'   => $projectid,
                            'memberid'		=> $memberid,
                            'projectrole'	=> $projectrole));


    if (!isset($featureid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('MEMBERCREATED'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'display', array('projectid' => $projectid)));

    return true;
}

?>