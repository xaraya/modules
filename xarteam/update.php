<?php

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