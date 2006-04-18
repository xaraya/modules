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
    if (!xarVarFetch('itspid',  'isset', $calendar_id,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('username',     'isset', $username,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('userid',       'isset', $userid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('pitemid',      'isset', $pitemid,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('planid',       'isset', $planid,       NULL, XARVAR_DONT_SET)) {return;}
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
        if (count($parts) > 1 && !empty($parts[1])) $planid = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $userid = $parts[2];
    }

    if (!xarSecurityCheck('AdminITSP')) return;

    if (empty($itspid) || $itspid == 'All' || !is_numeric($itspid)) {
        $itspid = 'All';
    }
    $title = '';
    if (empty($planid) || $planid == 'All' || !is_numeric($planid)) {
        $planid = 'All';
    } elseif ($planid >0) {

        $plan = xarModAPIFunc('itsp','user','get_plan',
                                 array('planid' => $planid));
        if (empty($plan)) {
            $title = '';
        } else {
            $title = $plan['planname'];
            // override whatever other params we might have here
       //     $calendar_id = $event['calendar_id'];
            /*
        // TODO: review when we can handle multiple categories and/or subtrees in privilege instances
            if (!empty($article['cids']) && count($article['cids']) == 1) {
                // if we don't have a category, or if we have one but this article doesn't belong to it
                if (empty($cid) || !in_array($cid, $article['cids'])) {
                    // we'll take that category
                    $cid = $article['cids'][0];
                }
            } else {
                // we'll take no categories
                $cid = 0;
            }
            $uid = $article['authorid'];
            $title = $article['title'];
            */
        }
    }

    // Get a userlist
    $itsplist = xarModApiFunc('itsp','user','getall');
// TODO: figure out how to handle groups of users and/or the current user (later)
    if (strtolower($userid) == 'myself') {
        $username = 'Myself';
    } elseif (empty($userid) || $userid == 'All' || (!is_numeric($userid) && (strtolower($userid) != 'myself'))) {
        $userid = 0;
        if (!empty($username)) {
            $user = xarModApiFunc('roles','user','get',array('name'=>$username));
            if (!empty($user) && !empty($username)) {
                if (strtolower($userid) == 'myself') {
                    $username = 'Myself';
                } else {
                    $userid = $user['uid'];
                }
            } else {
                $username = '';
            }
        }
    } else {
        $username = '';
    }
    //$itspid:$planid:$userid
    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($itspid) ? 'All' : $itspid;
    $newinstance[] = empty($planid) ? 'All' : $planid;
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
    if (!empty($planid) && isset($planlist[$planid])) {
        $planid = '';
    }

    if (empty($itspid)) {
        $itemtype = 2;
        $numitems = xarModAPIFunc('itsp','user','countitems',
                                  array('itemtype' => $itemtype));
    } else {
        $numitems = 0;
    }

    $data = array('planid'       => $planid,
                  'userid'       => $userid,
                  'itsplist'     => $itsplist,
                  'username'     => xarVarPrepForDisplay($username),
                  'planlist'     => $planlist,
                  'itspid'       => $itspid,
                  'title'        => xarVarPrepForDisplay($title),
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
