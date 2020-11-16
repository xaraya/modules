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
    if (!xarSecurity::check('AdminKeywords')) {
        return;
    }

    extract($args);

    if (!xarVar::fetch('moduleid', 'id', $moduleid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('itemtype', 'int:1:', $itemtype, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'id', $itemid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('apply', 'isset', $apply, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('extpid', 'isset', $extpid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('extname', 'isset', $extname, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('extrealm', 'isset', $extrealm, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('extmodule', 'isset', $extmodule, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('extcomponent', 'isset', $extcomponent, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('extinstance', 'isset', $extinstance, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('extlevel', 'isset', $extlevel, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('pparentid', 'isset', $pparentid, null, xarVar::DONT_SET)) {
        return;
    }

    if (!empty($extinstance)) {
        $parts = explode(':', $extinstance);
        if (count($parts) > 0 && !empty($parts[0])) {
            $moduleid = $parts[0];
        }
        if (count($parts) > 1 && !empty($parts[1])) {
            $itemtype = $parts[1];
        }
        if (count($parts) > 2 && !empty($parts[2])) {
            $itemid = $parts[2];
        }
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
        $pid = xarPrivileges::external(
            $extpid,
            $extname,
            $extrealm,
            $extmodule,
            $extcomponent,
            $newinstance,
            $extlevel,
            $pparentid
        );
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarController::redirect(xarController::URL(
            'privileges',
            'admin',
            'modifyprivilege',
            array('id' => $pid)
        ));
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
                  'extinstance'  => xarVar::prepForDisplay(join(':', $newinstance)),
                 );

    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;



    // Get the list of all modules currently hooked to categories
    $hookedmodlist = xarMod::apiFunc(
        'modules',
        'admin',
        'gethookedmodules',
        array('hookModName' => 'keywords')
    );
    if (!isset($hookedmodlist)) {
        $hookedmodlist = array();
    }
    $modlist = array();
    foreach ($hookedmodlist as $modname => $val) {
        if (empty($modname)) {
            continue;
        }
        $modid = xarMod::getRegId($modname);
        if (empty($modid)) {
            continue;
        }
        $modinfo = xarMod::getInfo($modid);
        $modlist[$modid] = $modinfo['displayname'];
    }



    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($moduleid) ? 'All' : $moduleid;
    $newinstance[] = empty($itemtype) ? 'All' : $itemtype;
    $newinstance[] = empty($itemid) ? 'All' : $itemid;

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarPrivileges::external($extpid, $extname, $extrealm, $extmodule, $extcomponent, $newinstance, $extlevel);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarController::redirect(xarController::URL(
            'privileges',
            'admin',
            'modifyprivilege',
            array('pid' => $pid)
        ));
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
                  'extinstance'  => xarVar::prepForDisplay(join(':', $newinstance)),
                 );

    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;
}
