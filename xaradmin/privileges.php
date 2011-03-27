<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Do something
 *
 * Standard function
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 * @throws none
 */
function crispbb_admin_privileges($args)
{
    extract($args);

    // Security Check
    if (!xarSecurityCheck('AdminCrispBB')) return;
    $now = time();
    if (!xarVarFetch('catid', 'str', $catid, 0, XARVAR_NOT_REQUIRED)) return; // empty, 'All', numeric or modulename
    if (!xarVarFetch('fid', 'str', $fid, 0, XARVAR_NOT_REQUIRED)) return; // empty, 'All', numeric
    if (!xarVarFetch('apply', 'str' , $apply , false, XARVAR_NOT_REQUIRED)) return; // boolean?
    if (!xarVarFetch('extpid', 'str', $extpid, '', XARVAR_NOT_REQUIRED)) return; // empty, 'All', numeric ?
    if (!xarVarFetch('extname', 'str', $extname, '', XARVAR_NOT_REQUIRED)) return; // ?
    if (!xarVarFetch('extrealm', 'str', $extrealm, '', XARVAR_NOT_REQUIRED)) return; // ?
    if (!xarVarFetch('extmodule','str', $extmodule, '', XARVAR_NOT_REQUIRED)) return; // ?
    if (!xarVarFetch('extcomponent', 'enum:All:Forum', $extcomponent)) return; // FIXME: is 'Type' needed?
    if (!xarVarFetch('extinstance', 'str:1', $extinstance, '', XARVAR_NOT_REQUIRED)) return; // somthing:somthing:somthing or empty
    if (!xarVarFetch('extlevel', 'str:1', $extlevel)) return;
    if (!xarVarFetch('pparentid',    'isset', $pparentid,    NULL, XARVAR_DONT_SET)) return;

// TODO: combine 'Item' and 'Type' instances someday ?

    if (!empty($extinstance)) {
        $parts = explode(':',$extinstance);
            if (count($parts) > 0 && !empty($parts[0])) $catid = $parts[0];
            if (count($parts) > 1 && !empty($parts[1])) $fid = $parts[1];
    }



    if (empty($catid) || $catid == 'All' || !is_numeric($catid)) {
        $catid = 0;
    }
    if (empty($fid) || $fid == 'All' || !is_numeric($fid)) {
        $fid = 0;
    }

    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($catid) ? 'All' : $catid;
    $newinstance[] = empty($fid) ? 'All' : $fid;

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,
                                  $newinstance,$extlevel,$pparentid);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarResponse::Redirect(xarModURL('privileges', 'admin', 'modifyprivilege',
                                      array('id' => $pid)));
        return true;
    }


    $forums = xarMod::apiFunc('crispbb', 'user', 'getforums');
    // get forum categories
    $mastertype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
        array('fid' => 0, 'component' => 'forum'));
    $basecats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'crispbb'));
    $parentcat = count($basecats) > 0 ? $basecats[0]['category_id'] : null;
    $categories = xarMod::apiFunc('categories', 'user', 'getchildren',
        array('cid' => $parentcat));
    $numitems = xarML('probably');



    $data = array(
                    'catid' => $catid,
                    'fid' => $fid,
                    'forums' => $forums,
                    'categories' => $categories,
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

    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;
}

?>