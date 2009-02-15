<?php
/**
 * Dossier module
 */
/**
 * Manage definition of instances for privileges (unfinished)
 *
 * @return array for template
 */
function accessmethods_admin_privileges($args)
{
    extract($args);

    // fixed params
    if (!xarVarFetch('userid',       'isset', $userid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('username',     'isset', $username,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('apply',        'isset', $apply,        NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extpid',       'isset', $extpid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extname',      'isset', $extname,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extrealm',     'isset', $extrealm,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extmodule',    'isset', $extmodule,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extcomponent', 'isset', $extcomponent, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extinstance',  'isset', $extinstance,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extlevel',     'isset', $extlevel,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('pparentid',    'isset', $pparentid,    NULL, XARVAR_DONT_SET)) {return;}

    if (!empty($extinstance)) {
        $parts = explode(':',$extinstance);
        if (count($parts) > 0 && !empty($parts[0])) $userid = $parts[0];
    }

// TODO: figure out how to handle groups of users and/or the current user (later)
    if (strtolower($userid) == 'myself') {
        $userid = 'Myself';
        $username = 'Myself';
    } elseif (empty($userid) || $userid == 'All' || (!is_numeric($userid) && (strtolower($userid) != 'myself'))) {
        $userid = 0;
        if (!empty($username)) {
            $user = xarModAPIFunc('roles', 'user', 'get',
                                  array('name' => $username));
            if (!empty($user) && !empty($user['uid'])) {
                if (strtolower($username) == 'myself') $userid = 'Myself';
                else $userid = $user['uid'];
            } else {
                $username = '';
            }
        }
    }

    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($userid) ? 'All' : $userid;

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,
                                  $newinstance,$extlevel,$pparentid);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarResponseRedirect(xarModURL('privileges', 'admin', 'modifyprivilege',
                                      array('pid' => $pid)));
        return true;
    }

    // get the list of current authors
    $userlist =  xarModAPIFunc('accessmethods','user','getowners');
    if (!empty($username) && isset($userlist[$userid])) {
        $username = '';
    }

    $data = array(
                  'cid'          => $cid,
                  'userid'          => $userid,
                  'username'          => $username,
                  'userlist'    => $userlist,
                  'extpid'       => $extpid,
                  'extname'      => $extname,
                  'extrealm'     => $extrealm,
                  'extmodule'    => $extmodule,
                  'extcomponent' => $extcomponent,
                  'extlevel'     => $extlevel,
                  'extinstance'  => xarVarPrepForDisplay(join(':',$newinstance)),
                  'pparentid'    => $pparentid,
                 );
                 
    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;
}

?>
