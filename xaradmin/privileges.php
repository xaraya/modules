<?php

/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/

/**
 * Manage definition of instances for privileges (unfinished)
 */

function xarbb_admin_privileges($args)
{
    extract($args);

    // Privilege Mask
    if (!xarVarFetch('fid',          'isset', $fid,         'All')) {return;}       // Forum ID
    if (!xarVarFetch('cid',          'isset', $cid,         'All', XARVAR_NOT_REQUIRED)) {return;}      // Categorie ID
    if (!xarVarFetch('cids',         'isset', $cids,         NULL, XARVAR_DONT_SET)) {return;}      // Categorie IDs


    // General Parameters
    if (!xarVarFetch('apply',        'isset', $apply,        NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extpid',       'isset', $extpid,       NULL, XARVAR_DONT_SET)) {return;}      // Privilidge ID
    if (!xarVarFetch('extname',      'isset', $extname,      NULL, XARVAR_DONT_SET)) {return;}      // Priviledge Name
    if (!xarVarFetch('extrealm',     'isset', $extrealm,     NULL, XARVAR_DONT_SET)) {return;}      // Priv Realm
    if (!xarVarFetch('extmodule',    'isset', $extmodule,    NULL, XARVAR_DONT_SET)) {return;}      // ....
    if (!xarVarFetch('extcomponent', 'isset', $extcomponent, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extinstance',  'isset', $extinstance,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extlevel',     'isset', $extlevel,     NULL, XARVAR_DONT_SET)) {return;}

    if (!empty($extinstance)) {
        $parts = explode(':', $extinstance);
        if (count($parts) > 0 && !empty($parts[0])) $cid = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $fid = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $fname = $parts[2];
    }

    if (!xarSecurityCheck('AdminxarBB', 1, 'Forum', "$cid:$fid")) return;

    // TODO: figure out how to handle more than 1 category in instances
    if (isset($cids) && is_array($cids)) {
        foreach ($cids as $catid) {
            if (!empty($catid)) {
                $cid = $catid;
                // bail out for now
                break;
            }
        }
    }

    // TODO: figure out how to handle groups of users and/or the current user (later)

    $filter = array();
    if ($cid != "All") $filter["catids"] = array($cid);
    if ($fid != "All") $filter["fid"] = $fid;

    $numitems = xarModAPIFunc('xarbb', 'user', 'countforums', array($filter));

    if ($cid != 'All') {
        $fids = xarModAPIFunc('xarbb', 'user', 'getallforums', array("assoc" => "fid", "catid" => $cid));
    } else {
        $fids = xarModAPIFunc('xarbb', 'user', 'getallforums', array("assoc" => "fid"));
    }

    if (!in_array($fid, array_keys($fids))) $fid = 'All';

    // Define the new instance
    $newinstance = array();
    $newinstance[] = (empty($cid) ? 'All' : $cid);
    $newinstance[] = (empty($fid) ? 'All' : $fid);

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid, $extname, $extrealm, $extmodule, $extcomponent, $newinstance, $extlevel);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarResponseRedirect(xarModURL('privileges', 'admin', 'modifyprivilege', array('pid' => $pid)));
        return true;
    }

    $data = array(
        'cid'          => $cid,
        'fid'          => $fid,
        'cids'         => $cids,
        'fids'         => $fids,
        'numitems'     => $numitems,
        'extpid'       => $extpid,
        'extname'      => $extname,
        'extrealm'     => $extrealm,
        'extmodule'    => $extmodule,
        'extcomponent' => $extcomponent,
        'extlevel'     => $extlevel,
        'extinstance'  => xarVarPrepForDisplay(join(':', $newinstance)),
    );

    $catlist = array();

    // Master category IDs for this module.
    $cidstring = xarModGetVar('xarBB', 'mastercids');

    if (!empty($cidstring)) {
        $rootcats = explode (';', $cidstring);
        foreach ($rootcats as $catid) {
            $catlist[$catid] = 1;
        }
    }

    $seencid = array();
    if (!empty($cid)) {
        $seencid[$cid] = 1;
    }

    $data['cats'] = array();

    foreach (array_keys($catlist) as $catid) {
        $data['cats'][] = xarModAPIFunc(
            'categories', 'visual', 'makeselect',
            array(
                'cid' => $catid,
                'return_itself' => true,
                'values' => &$seencid,
                'multiple' => 0,
                'javascript' => 'onchange="submit()"'
            )
        );
    }

    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;
}

?>