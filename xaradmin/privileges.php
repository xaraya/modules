<?php
/**
 * Manage definition of instances for privileges
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 * @author mikespub 
 */
/**
 * Manage definition of instances for privileges (unfinished)
 *
 * @return array for template
 */
function content_admin_privileges($args)
{
    extract($args);

    // fixed params
    if (!xarVarFetch('ctid',         'isset', $ctid,         NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('cid',          'isset', $cid,          NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('cids',         'isset', $cids,         NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('uid',          'isset', $uid,          NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('author',       'isset', $author,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('aid',          'isset', $itemid,          NULL, XARVAR_DONT_SET)) {return;}
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
        if (count($parts) > 0 && !empty($parts[0])) $ctid = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $cid = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $uid = $parts[2];
        if (count($parts) > 3 && !empty($parts[3])) $itemid = $parts[3];
    }

    if (empty($ctid) || $ctid == 'All' || !is_numeric($ctid)) {
        $ctid = 0;
        if (!xarSecurityCheck('AdminContent')) return;
    } else {
        if (!xarSecurityCheck('AdminContent',1,'Item',"$ctid:All:All:All")) return;
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

    if (empty($itemid) || $itemid == 'All' || !is_numeric($itemid)) {
        $itemid = 0;
    }
    $title = '';
    if (!empty($itemid)) {
        $item = xarModAPIFunc('Content','user','get',
                                 array('aid'      => $itemid,
                                       'withcids' => true));
        if (empty($item)) {
            $itemid = 0;
        } else {
            // override whatever other params we might have here
            $ctid = $item['pubtypeid'];
        // TODO: review when we can handle multiple categories and/or subtrees in privilege instances
            if (!empty($item['cids']) && count($item['cids']) == 1) {
                // if we don't have a category, or if we have one but this article doesn't belong to it
                if (empty($cid) || !in_array($cid, $item['cids'])) {
                    // we'll take that category
                    $cid = $item['cids'][0];
                }
            } else {
                // we'll take no categories
                $cid = 0;
            }
            $uid = $item['authorid'];
            $title = $item['title'];
        }
    }

// TODO: figure out how to handle groups of users and/or the current user (later)
    if (strtolower($uid) == 'myself') {
        $uid = 'Myself';
        $author = 'Myself';
    } elseif (empty($uid) || $uid == 'All' || (!is_numeric($uid) && (strtolower($uid) != 'myself'))) {
        $uid = 0;
        if (!empty($author)) {
            $user = xarModAPIFunc('roles', 'user', 'get',
                                  array('name' => $author));
            if (!empty($user) && !empty($user['uid'])) {
                if (strtolower($author) == 'myself') $uid = 'Myself';
                else $uid = $user['uid'];
            } else {
                $author = '';
            }
        }
    } else {
        $author = '';
/*
        $user = xarModAPIFunc('roles', 'user', 'get',
                              array('uid' => $uid));
        if (!empty($user) && !empty($user['name'])) {
            $author = $user['name'];
        }
*/
    }

    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($ctid) ? 'All' : $ctid;
    $newinstance[] = empty($cid) ? 'All' : $cid;
    $newinstance[] = empty($uid) ? 'All' : $uid;
    $newinstance[] = empty($itemid) ? 'All' : $itemid;

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
    $authorlist =  xarModAPIFunc('Content','user','getauthors',
                                 array('ctid' => $ctid,
                                       'cids' => empty($cid) ? array() : array($cid)));
    if (!empty($author) && isset($authorlist[$uid])) {
        $author = '';
    }

    if (empty($itemid)) {
        $numitems = xarModAPIFunc('Content','user','countitems',
                                  array('ctid' => $ctid,
                                        'cids' => empty($cid) ? array() : array($cid),
                                        'authorid' => $uid));
    } else {
        $numitems = 1;
    }

    $data = array(
                  'ctid'         => $ctid,
                  'cid'          => $cid,
                  'uid'          => $uid,
                  'author'       => xarVarPrepForDisplay($author),
                  'authorlist'   => $authorlist,
                  'aid'          => $itemid,
                  'title'        => xarVarPrepForDisplay($title),
                  'numitems'     => $numitems,
                  'extpid'       => $extpid,
                  'extname'      => $extname,
                  'extrealm'     => $extrealm,
                  'extmodule'    => $extmodule,
                  'extcomponent' => $extcomponent,
                  'extlevel'     => $extlevel,
                  'extinstance'  => xarVarPrepForDisplay(join(':',$newinstance)),
                  'pparentid'    => $pparentid,
                 );

    // Get publication types
    $data['pubtypes'] = xarModAPIFunc('Content','user','getpubtypes');

    $catlist = array();
    if (!empty($ctid)) {
        $cidstring = xarModGetVar('Content', 'mastercids.'.$ctid);
        if (!empty($cidstring)) {
            $rootcats = explode (';', $cidstring);
            foreach ($rootcats as $catid) {
                $catlist[$catid] = 1;
            }
        }
        if (empty($data['pubtypes'][$ctid]['config']['authorid']['label'])) {
            $data['showauthor'] = 0;
        } else {
            $data['showauthor'] = 1;
        }
    } else {
        foreach (array_keys($data['pubtypes']) as $pubid) {
            $cidstring = xarModGetVar('Content', 'mastercids.'.$pubid);
            if (!empty($cidstring)) {
                $rootcats = explode (';', $cidstring);
                foreach ($rootcats as $catid) {
                    $catlist[$catid] = 1;
                }
            }
        }
        $data['showauthor'] = 1;
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
