<?php

/**
 * Manage definition of instances for privileges (unfinished)
 */
function categories_admin_privileges($args)
{
    // Security Check
    if (!xarSecurityCheck('AdminCategories')) return;

    // fixed params
    list($cid,
         $cids,
         $moduleid,
         $itemtype,
         $itemid,
         $apply,
         $extpid,
         $extname,
         $extrealm,
         $extmodule,
         $extcomponent,
         $extinstance,
         $extlevel) = xarVarCleanFromInput('cid',
                                           'cids',
                                           'moduleid',
                                           'itemtype',
                                           'itemid',
                                           'apply',
                                           'extpid',
                                           'extname',
                                           'extrealm',
                                           'extmodule',
                                           'extcomponent',
                                           'extinstance',
                                           'extlevel');
    extract($args);

    if (!empty($extinstance)) {
        $parts = explode(':',$extinstance);
        if (count($parts) > 0 && !empty($parts[0])) $moduleid = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $itemtype = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $itemid = $parts[2];
        if (count($parts) > 3 && !empty($parts[3])) $cid = $parts[3];
    }

    // Get the list of all modules currently hooked to categories
    $hookedmodlist = xarModAPIFunc('modules','admin','gethookedmodules',
                                   array('hookModName' => 'categories'));
    if (!isset($hookedmodlist)) {
        $hookedmodlist = array();
    }
    $modlist = array();
    foreach ($hookedmodlist as $modname => $val) {
        if (empty($modname)) continue;
        $modid = xarModGetIDFromName($modname);
        if (empty($modid)) continue;
        $modinfo = xarModGetInfo($modid);
        $modlist[$modid] = $modinfo['displayname'];
    }

    if (empty($moduleid) || $moduleid == 'All' || !is_numeric($moduleid)) {
        $moduleid = 0;
    }
    if (empty($itemtype) || $itemtype == 'All' || !is_numeric($itemtype)) {
        $itemtype = 0;
    }
    if (empty($itemid) || $itemid == 'All' || !is_numeric($itemid)) {
        $itemid = 0;
    }
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

    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($moduleid) ? 'All' : $moduleid;
    $newinstance[] = empty($itemtype) ? 'All' : $itemtype;
    $newinstance[] = empty($itemid) ? 'All' : $itemid;
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

    if (!empty($moduleid)) {
        $numitems = xarModAPIFunc('categories','user','countitems',
                                  array('modid' => $moduleid,
                                        'cids'  => (empty($cid) ? null : array($cid))
                                       ));
    } else {
        $numitems = xarML('probably');
    }

    $data = array(
                  'cid'          => $cid,
                  'moduleid'     => $moduleid,
                  'itemtype'     => $itemtype,
                  'itemid'       => $itemid,
                  'modlist'      => $modlist,
                  'numitems'     => $numitems,
                  'extpid'       => $extpid,
                  'extname'      => $extname,
                  'extrealm'     => $extrealm,
                  'extmodule'    => $extmodule,
                  'extcomponent' => $extcomponent,
                  'extlevel'     => $extlevel,
                  'extinstance'  => xarVarPrepForDisplay(join(':',$newinstance)),
                 );

    $catlist = array();
    if (!empty($moduleid)) {
        $modinfo = xarModGetInfo($moduleid);
        $modname = $modinfo['name'];
        if (!empty($itemtype)) {
            $cidstring = xarModGetVar($modname, 'mastercids.'.$itemtype);
            if (!empty($cidstring)) {
                $rootcats = explode (';', $cidstring);
                foreach ($rootcats as $catid) {
                    $catlist[$catid] = 1;
                }
            }
        } else {
            $cidstring = xarModGetVar($modname, 'mastercids');
            if (!empty($cidstring)) {
                $rootcats = explode (';', $cidstring);
                foreach ($rootcats as $catid) {
                    $catlist[$catid] = 1;
                }
            }
        }
    } else {
        // something with categories
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
                                              'select_itself' => true,
                                              'values' => &$seencid,
                                              'multiple' => 0,
                                              'javascript' => 'onchange="submit()"'));
    }

    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;
}

?>
