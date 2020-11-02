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
    if (!xarSecurity::check('AdminCrispBB')) return;
    $now = time();
    if (!xarVar::fetch('catid', 'str', $catid, 0, xarVar::NOT_REQUIRED)) return; // empty, 'All', numeric or modulename
    if (!xarVar::fetch('fid', 'str', $fid, 0, xarVar::NOT_REQUIRED)) return; // empty, 'All', numeric
    if (!xarVar::fetch('apply', 'str' , $apply , false, xarVar::NOT_REQUIRED)) return; // boolean?
    if (!xarVar::fetch('extpid', 'str', $extpid, '', xarVar::NOT_REQUIRED)) return; // empty, 'All', numeric ?
    if (!xarVar::fetch('extname', 'str', $extname, '', xarVar::NOT_REQUIRED)) return; // ?
    if (!xarVar::fetch('extrealm', 'str', $extrealm, '', xarVar::NOT_REQUIRED)) return; // ?
    if (!xarVar::fetch('extmodule','str', $extmodule, '', xarVar::NOT_REQUIRED)) return; // ?
    if (!xarVar::fetch('extcomponent', 'enum:All:Forum', $extcomponent)) return; // FIXME: is 'Type' needed?
    if (!xarVar::fetch('extinstance', 'str:1', $extinstance, '', xarVar::NOT_REQUIRED)) return; // somthing:somthing:somthing or empty
    if (!xarVar::fetch('extlevel', 'str:1', $extlevel)) return;
    if (!xarVar::fetch('pparentid',    'isset', $pparentid,    NULL, xarVar::DONT_SET)) return;

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
        xarController::redirect(xarController::URL('privileges', 'admin', 'modifyprivilege',
                                      array('id' => $pid)));
        return true;
    }


    $forums = xarMod::apiFunc('crispbb', 'user', 'getforums');
    // get forum categories
    $mastertype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
        array('fid' => 0, 'component' => 'forum'));
    $basecats = xarMod::apiFunc('crispbb','user','getcatbases');
    $basecid = count($basecats) > 0 ? $basecats[0] : 0;
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
                  'extinstance'  => xarVar::prepForDisplay(join(':',$newinstance)),
                  'pparentid'    => $pparentid,
                 );

    return $data;
}

?>