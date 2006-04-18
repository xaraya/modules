<?php
/**
 * Julian module
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Manage definition of instances for privileges (unfinished)
 *
 * The mask will have the form of $event_id:$organizer:$calendar_id:$cid
 * $organizer here is the user that entered the event, the name might be confusing
 * @author MichelV <michelv@xaraya.com>
 * @since 18 April 2006
 * @return array for template
 */
function itsp_admin_privileges($args)
{
    extract($args);

    // fixed params
    if (!xarVarFetch('itspid',       'isset', $itspid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('username',     'isset', $username,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('userid',       'isset', $userid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('uid',          'isset', $uid,          NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('pitemid',      'isset', $pitemid,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('pid',          'isset', $pid,          NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('apply',        'isset', $apply,        NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extpid',       'isset', $extpid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extname',      'isset', $extname,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extrealm',     'isset', $extrealm,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extmodule',    'isset', $extmodule,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extcomponent', 'isset', $extcomponent, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extinstance',  'isset', $extinstance,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extlevel',     'isset', $extlevel,     NULL, XARVAR_DONT_SET)) {return;}

    if (!empty($extinstance)) {
        $parts = explode(':',$extinstance);
        if (count($parts) > 0 && !empty($parts[0])) $itspid = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $pid = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $userid = $parts[2];
    }
    // Security check
    if (!xarSecurityCheck('AdminITSP')) return;

    if (empty($itspid) || $itspid == 'All' || !is_numeric($itspid)) {
        $itspid = 'All';
    }


    if (empty($pid) || $pid == 'All' || !is_numeric($pid) || $pid == 0) {
        $pid = 0;
    } elseif ($pid >0) {
        $plan = xarModAPIFunc('itsp','user','get_plan',
                                 array('planid' => $pid));
        if (empty($plan)) {
            $pid = 0;
        }
    }

    // Get a userlist
    $itsplist = xarModApiFunc('itsp','user','getall');

    if (strtolower($userid) == 'myself') {
        $userid = 'Myself';
        $uid =0;
    } elseif (empty($userid) || $userid == 'All' || (!is_numeric($userid) && (strtolower($userid) != 'myself'))) {
        $userid = 0;
    } elseif (!empty($userid) && (strtolower($username) != 'myself')) {
        $user = xarModApiFunc('roles','user','get',array('uname'=>$userid));
        if (!empty($user)) {
            if (strtolower($userid) == 'myself') {
                $uid = 0;
            } else {
                $uid = $user['uid'];
            }
        } else {
            $username = '';
        }

    } elseif (is_numeric($userid)) {
        $username = xarUserGetVar('name',$userid);
    } else {
        $userid = 0;
        $username = '';
    }
    //$itspid:$planid:$userid
    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($itspid) ? 'All' : $itspid;
    $newinstance[] = empty($pid) ? 'All' : $pid;
    $newinstance[] = empty($userid) ? 'All' : $userid;

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,$newinstance,$extlevel);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarResponseRedirect(xarModURL('privileges', 'admin', 'modifyprivilege',
                                      array('pid' => $pid)));
        return true;
    }

    // get the list of plans
    $planlist =  xarModAPIFunc('itsp','user','getall_plans');


    if (empty($itspid)) {
        $itemtype = 2;
        $numitems = xarModAPIFunc('itsp','user','countitems',
                                  array('itemtype' => $itemtype));
    } else {
        $numitems = 0;
    }

    $data = array('pid'          => $pid,
                  'userid'       => $userid,
                  'uid'          => $uid,
                  'itsplist'     => $itsplist,
                  'username'     => xarVarPrepForDisplay($username),
                  'planlist'     => $planlist,
                  'itspid'       => $itspid,
                  'numitems'     => $numitems,
                  'extpid'       => $extpid,
                  'extname'      => $extname,
                  'extrealm'     => $extrealm,
                  'extmodule'    => $extmodule,
                  'extcomponent' => $extcomponent,
                  'extlevel'     => $extlevel,
                  'extinstance'  => xarVarPrepForDisplay(join(':',$newinstance)),
                 );

    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;
}

?>
