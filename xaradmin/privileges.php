<?php
/**
 * Julian module
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Manage definition of instances for privileges (unfinished)
 *
 * The mask will have the form of $event_id:$organizer:$calendar_id:$cid
 * $organizer here is the user that entered the event, the name might be confusing
 * @author MichelV <michelv@xaraya.com>
 * @since feb 2006
 * @return array for template
 */
function julian_admin_privileges($args)
{
    extract($args);

    // fixed params
    if (!xarVarFetch('calendar_id',  'isset', $calendar_id,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('cid',          'isset', $cid,          NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('cids',         'isset', $cids,         NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('uid',          'isset', $uid,          NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('organizer',    'isset', $organizer,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('event_id',     'isset', $event_id,     NULL, XARVAR_DONT_SET)) {return;}
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
        if (count($parts) > 0 && !empty($parts[0])) $event_id    = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $uid         = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $calendar_id = $parts[2];
        if (count($parts) > 3 && !empty($parts[3])) $cid         = $parts[3];
    }

    if (empty($calendar_id) || $calendar_id == 'All' || !is_numeric($calendar_id)) {
        $calendar_id = 0;
        if (!xarSecurityCheck('AdminJulian')) return;
    } else {
        if (!xarSecurityCheck('AdminJulian',1,'Item',"$calendar_id:All:All:All")) return;
    }

// TODO: do something with cid for security check

// TODO: figure out how to handle more than 1 category in instances
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

    if (empty($event_id) || $event_id == 'All' || !is_numeric($event_id)) {
        $event_id = 0;
    }
    $title = '';
    if (!empty($event_id)) {
        $event = xarModAPIFunc('julian','user','get',
                                 array('event_id' => $event_id));
        if (empty($event)) {
            $event_id = 0;
        } else {
            // override whatever other params we might have here
            $calendar_id = $event['calendar_id'];
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

// TODO: figure out how to handle groups of users and/or the current user (later)
    if (strtolower($uid) == 'myself') {
        $uid = 'Myself';
        $organizer = 'Myself';
    } elseif (empty($uid) || $uid == 'All' || (!is_numeric($uid) && (strtolower($uid) != 'myself'))) {
        $uid = 0;
        if (!empty($organizer)) {
            $user = xarModAPIFunc('roles', 'user', 'get',
                                  array('name' => $organizer));
            if (!empty($user) && !empty($user['uid'])) {
                if (strtolower($organizer) == 'myself') $uid = 'Myself';
                else $uid = $user['uid'];
            } else {
                $organizer = '';
            }
        }
    } else {
        $organizer = '';
/*
        $user = xarModAPIFunc('roles', 'user', 'get',
                              array('uid' => $uid));
        if (!empty($user) && !empty($user['name'])) {
            $author = $user['name'];
        }
*/
    }
    // $event_id:$organizer:$calendar_id:$cid
    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($event_id) ? 'All' : $event_id;
    $newinstance[] = empty($uid)   ? 'All' : $uid;
    $newinstance[] = empty($calendar_id) ? 'All' : $calendar_id;
    $newinstance[] = empty($cid) ? 'All' : $cid;

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

    // get the list of current organizers
    $organizerlist =  xarModAPIFunc('julian','user','getorganizers',
                                 array('calendar_id' => $calendar_id,
                                       // TODO: work out cids in this case
                                       'cids' => empty($cid) ? array() : array($cid)));
    if (!empty($organizer) && isset($organizerlist[$uid])) {
        $organizer = '';
    }

    if (empty($event_id)) {
        $numitems = xarModAPIFunc('julian','user','countevents',
                                  array('calendar_id' => $calendar_id,
                                        'cids' => empty($cid) ? array() : array($cid),
                                        'organizer' => $uid));
    } else {
        $numitems = 1;
    }

    $data = array(
                  'calendar_id'  => $calendar_id,
                  'cid'          => $cid,
                  'uid'          => $uid,
                  'organizer'    => xarVarPrepForDisplay($organizer),
                  'organizerlist'   => $organizerlist,
                  'event_id'     => $event_id,
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

    // TODO: implement more calendars
    $data['calendarids'] = array(1);//xarModAPIFunc('julian','user','getpubtypes');

    $catlist = array();
    $cidstring = xarModGetVar('julian', 'mastercids');
    if (!empty($cidstring)) {
        $rootcats = explode (';', $cidstring);
        foreach ($rootcats as $catid) {
            $catlist[$catid] = 1;
        }
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
