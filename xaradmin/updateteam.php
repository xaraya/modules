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
function xproject_admin_updateteam($args)
{
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('teamimplodedlist', 'str::', $teamimplodedlist, $teamimplodedlist, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, $returnurl, XARVAR_NOT_REQUIRED)) return;
    
    extract($args);

    if(!xarModLoad('addressbook', 'user')) return;

    if(empty($returnurl)) $returnurl = xarModURL('xproject', 'admin', 'display', array('projectid' => $projectid));

    if (!xarSecConfirmAuthKey()) return;
    
    $newmemberlist = explode(",",$teamimplodedlist);

    $teamlist = xarModAPIFunc('xproject', 'team', 'getall', array('projectid' => $projectid));
    
    if(!is_array($teamlist)) { return gettype($teamlist); } /* {
        $msg = xarML('No #(1) available for projectid #(2)',
                    'team members', xarVarPrepForDisplay($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SYSTEM_ERROR',
                       new SystemException($msg));
        return;
    }*/
    
    $newmembers = array();
    foreach($newmemberlist as $newmemberid) {
        $newmembers[] = $newmemberid;
    }
    
    $existingmembers = array();
    foreach($teamlist as $memberinfo) {
        $existingmembers[] = $memberinfo['memberid'];
        if(!in_array($memberinfo['memberid'], $newmembers)) {
            // REMOVE TEAM MEMBER
            if (!xarModAPIFunc('xproject',
                             'team',
                             'delete',
                             array('projectid' => $projectid,
                                    'memberid' => $memberinfo['memberid']))) {
                return;
            }
        }
    }
    
    if(count($newmemberlist) > 0) {
        foreach($newmemberlist as $newmemberid) {
            if($newmemberid) {
                if(!in_array($newmemberid, $existingmembers)) {
                    // ADD TEAM MEMBER
                    if (!xarModAPIFunc('xproject',
                                        'team',
                                        'create',
                                        array('projectid'   => $projectid,
                                            'memberid'      => $newmemberid,
                                            'projectrole'   => "Team Member"))) {
                        return;
                    }
                }
            }
        }
    }
    
    xarSessionSetVar('statusmsg', xarML('Project Team Updated'));

    xarResponseRedirect($returnurl);

    return true;
}

?>