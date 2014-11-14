<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */

/**
 * Manage definition of instances for privileges (unfinished)
 * @param array $args all privilege parts
 * @return array with the new privileges
 */
function keywords_admin_privileges($args)
{
    // Security Check
    if (!xarSecurityCheck('AdminKeywords')) return;

    extract($args);

    if (!xarVarFetch('moduleid',     'id', $moduleid,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('itemtype',     'int:1:', $itemtype,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('itemid',       'id', $itemid,       NULL, XARVAR_DONT_SET)) {return;}
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
        if (count($parts) > 0 && !empty($parts[0])) $moduleid = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $itemtype = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $itemid = $parts[2];
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

    // get the list of modules (and their itemtypes) keywords is currently hooked to
    $subjects = xarMod::apiFunc('keywords', 'hooks', 'getsubjects');

    $modlist = array();
    $typelist = array();
    foreach ($subjects as $modname => $modinfo) {
        $modlist[$modinfo['regid']] = array('id' => $modinfo['regid'], 'name' =>$modinfo['displayname']);
        if ($moduleid == $modinfo['regid'] && !empty($modinfo['itemtypes'])) {
            foreach ($modinfo['itemtypes'] as $typeid => $typeinfo) {
                $typelist[$typeid] = array('id' => $typeid, 'name' => $typeid . ' - ' .$typeinfo['label']);
            }
        }
    }

    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($moduleid) ? 'All' : $moduleid;
    $newinstance[] = empty($itemtype) ? 'All' : $itemtype;
    $newinstance[] = empty($itemid) ? 'All' : $itemid;

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,
                                  $newinstance,$extlevel,$pparentid);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarController::redirect(xarModURL('privileges', 'admin', 'modifyprivilege',
                                      array('id' => $pid)));
        return true;
    }

/*
    if (!empty($moduleid)) {
        $numitems = xarMod::apiFunc('categories','user','countitems',
                                  array('modid' => $moduleid,
                                        'cids'  => (empty($cid) ? null : array($cid))
                                       ));
    } else {
        $numitems = xarML('probably');
    }
*/
    $numitems = xarML('probably');

    $extlevels = array(
        0 => array('id' => 0, 'name' => 'No Access'),
        200 => array('id' => 200, 'name' => 'Read Access'),
        300 => array('id' => 300, 'name' => 'Add Access'),
        700 => array('id' => 700, 'name' => 'Manage Access'),
        800 => array('id' => 800, 'name' => 'Admin Access'),
    );

    $data = array(
                  'moduleid'     => $moduleid,
                  'itemtype'     => $itemtype,
                  'itemid'       => $itemid,
                  'modlist'      => $modlist,
                  'typelist'     => $typelist,
                  'numitems'     => $numitems,
                  'extpid'       => $extpid,
                  'extname'      => $extname,
                  'extrealm'     => $extrealm,
                  'extmodule'    => $extmodule,
                  'extcomponent' => $extcomponent,
                  'extlevel'     => $extlevel,
                  'extlevels'    => $extlevels,
                  'pparentid'    => $pparentid,
                  'extinstance'  => xarVarPrepForDisplay(join(':',$newinstance)),
                 );

    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;



    // Get the list of all modules currently hooked to categories
    $hookedmodlist = xarMod::apiFunc('modules','admin','gethookedmodules',
                                   array('hookModName' => 'keywords'));
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



    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($moduleid) ? 'All' : $moduleid;
    $newinstance[] = empty($itemtype) ? 'All' : $itemtype;
    $newinstance[] = empty($itemid) ? 'All' : $itemid;

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,$newinstance,$extlevel);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarController::redirect(xarModURL('privileges', 'admin', 'modifyprivilege',
                                      array('pid' => $pid)));
        return true;
    }

/*
    if (!empty($moduleid)) {
        $numitems = xarMod::apiFunc('categories','user','countitems',
                                  array('modid' => $moduleid,
                                        'cids'  => (empty($cid) ? null : array($cid))
                                       ));
    } else {
        $numitems = xarML('probably');
    }
*/
    $numitems = xarML('probably');

    $data = array(
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

    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;
}

?>
