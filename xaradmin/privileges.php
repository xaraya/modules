<?php
/**
 * Dossier module
 */
/**
 * Manage definition of instances for privileges (unfinished)
 *
 * @return array for template
 */
function dossier_admin_privileges($args)
{
    extract($args);

    // fixed params
    if (!xarVarFetch('cid',          'isset', $cid,          NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('cids',         'isset', $cids,         NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('userid',       'isset', $userid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('username',     'isset', $username,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('company',      'isset', $company,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('companyname',    'isset', $companyname,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('ownerid',      'isset', $ownerid,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('ownername',    'isset', $ownername,      NULL, XARVAR_DONT_SET)) {return;}
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
        if (count($parts) > 0 && !empty($parts[0])) $cid = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $userid = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $company = $parts[2];
        if (count($parts) > 3 && !empty($parts[3])) $ownerid = $parts[3];
    }
    
    if (empty($cid) || $cid == 'All' || !is_numeric($cid)) {
        $cid = 0;
    }
    if (empty($cid) && isset($cids) && is_array($cids)) {
        foreach ($cids as $catid) {
            if (!empty($catid)) {
                $cid = $catid;
                // bail out for now
                break;
            }
        }
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
    if (strtolower($ownerid) == 'myself') {
        $ownerid = 'Myself';
        $ownername = 'Myself';
    } elseif (empty($ownerid) || $ownerid == 'All' || (!is_numeric($ownerid) && (strtolower($ownerid) != 'myself'))) {
        $ownerid = 0;
        if (!empty($ownername)) {
            $user = xarModAPIFunc('roles', 'user', 'get',
                                  array('name' => $ownername));
            if (!empty($user) && !empty($user['uid'])) {
                if (strtolower($ownername) == 'myself') $ownerid = 'Myself';
                else $ownerid = $user['uid'];
            } else {
                $ownername = '';
            }
        }
    }

    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($cid) ? 'All' : $cid;
    $newinstance[] = empty($userid) ? 'All' : $userid;
    $newinstance[] = empty($company) ? 'All' : $company;
    $newinstance[] = empty($ownerid) ? 'All' : $ownerid;

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
    $userlist =  xarModAPIFunc('dossier','user','getowners');
    if (!empty($username) && isset($userlist[$userid])) {
        $username = '';
    }
    if (!empty($ownername) && isset($userlist[$ownerid])) {
        $ownername = '';
    }
    
    $companylist =  xarModAPIFunc('dossier','user','getcompanies');

    $data = array(
                  'cid'          => $cid,
                  'userid'          => $userid,
                  'username'          => $username,
                  'company'       => xarVarPrepForDisplay($company),
                  'companyname'       => xarVarPrepForDisplay($companyname),
                  'companylist'       => $companylist,
                  'ownerid'          => $ownerid,
                  'ownername'          => $ownername,
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

    $catlist = array();
    $mastercid = xarModGetVar('dossier', 'contactcid');
    if (!empty($mastercid)) {
        $catlist[$mastercid] = 1;
    }

    $seencid = array();
    if (!empty($cid)) {
        $seencid[$cid] = 1;
/*
        $data['catinfo'] = xarModAPIFunc('categories',
                                         'user',
                                         'getcatinfo',
                                         array('cid' => $cid));
*/
    }

    $data['cats'] = array();
    foreach (array_keys($catlist) as $catid) {
        $data['cats'][] = xarModAPIFunc('categories',
                                        'visual',
                                        'makeselect',
                                        Array('cid' => $catid,
                                              'return_itself' => true,
                                              'values' => &$seencid,
                                              'multiple' => 0,
                                              'javascript' => 'onchange="submit()"'));
    }
                 
    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;
}

?>
