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
 * Standard function to re-order forum categories
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_admin_reordercats($args)
{
    if (!xarSecurityCheck('AdminCrispBB')) return;

    // Get parameters
    if (!xarVarFetch('catid', 'id', $catid)) return;
    if (!xarVarFetch('direction', 'enum:up:down', $direction)) return;
    $now = time();
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    $cids = array($catid);

    $mastertype = xarModAPIFunc('crispbb', 'user','getitemtype', array('fid' => 0, 'component' => 'forum'));
    $mastercids = xarModGetVar('crispbb', 'mastercids.'.$mastertype);
    $parentcat = array_shift(explode(';', $mastercids));

    $categories = xarModAPIFunc('categories', 'user', 'getchildren',
        array('cid' => $parentcat));

    if (!isset($categories[$catid])) return;

    $refs = array();
    $i = 0;
    foreach ($categories as $refcat => $catinfo) {
        $refs[$i] = $catinfo;
        $i++;
    }

    foreach ($refs as $k => $cat) {
        if ($cat['cid'] == $catid) {
            if ($direction == 'up') {
                if (isset($refs[$k-1])) {
                    $refcid[$catid] = $refs[$k-1]['cid'];
                    $position[$catid] = 1;
                }
            } elseif ($direction == 'down') {
                if (isset($refs[$k+1])) {
                    $refcid[$catid] = $refs[$k+1]['cid'];
                    $position[$catid] = 2;
                }
            }
            $name[$catid] = $cat['name'];
            $description[$catid] = $cat['description'];
            $image[$catid] = $cat['image'];
            break;
        }
    }

    $moving[$catid] = true;
    $catexists[$catid] = true;
    $repeat = 1;
    $creating = false;
    if (!xarVarFetch('refcid', 'list:int:0', $refcid)) return;
    if (!xarVarFetch('position', 'list:enum:1:2:3:4', $position)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    //Reverses the order of cids with the 'last children' option:
    //Look at bug #997

    $old_cids = $cids;
    $cids = array();
    foreach ($old_cids as $key => $cid) {
        //Empty -> Creating Cats (ALL OF THEM should have empty cids!)
        if (empty($cid)) {
            $cid = $key;
            $creating = true;
        }

        if (intval($position[$cid]) == 3 ||
            intval($position[$cid]) == 2 ) {
            array_unshift ($cids, $cid);
        } else {
            array_push ($cids, $cid);
        }
    }
    if (count($cids) > $repeat) {
        $cids = array_slice($cids,0,$repeat);
    }
    foreach ($cids as $cid) {
        if (empty($name[$cid])) {
            continue;
        }
        switch (intval($position[$cid])) {
            case 1: // above - same level
            default:
                $rightorleft = 'left';
                $inorout = 'out';
                break;
            case 2: // below - same level
                $rightorleft = 'right';
                $inorout = 'out';
                break;
            case 3: // below - child category
                $rightorleft = 'right';
                $inorout = 'in';
                break;
            case 4: // above - child category
                $rightorleft = 'left';
                $inorout = 'in';
                break;
        }

        // call transform input hooks
        /*Not working, let's come back to it.
        // TODO allow input transforms
        $description[$cid]['transform'] = array($description);
        $description[$cid] = xarModCallHooks('item', 'transform-input', 0, $description,
                                             'categories', 0);
        */
        // Pass to API
        if (!$creating) {
            // Updating a category. Check we have privilage to do so.
            if (!xarSecurityCheck('EditCategories', 1, 'Category', "All:$cid")) return;
            if (!xarModAPIFunc('categories', 'admin', 'updatecat',
                array(
                    'cid'         => $cid,
                    'name'        => $name[$cid],
                    'description' => $description[$cid],
                    'image'       => $image[$cid],
                    'moving'      => $moving[$cid],
                    'refcid'      => $refcid[$cid],
                    'inorout'     => $inorout,
                    'rightorleft' => $rightorleft
                )
            )) return;
        }
    }
    xarResponseRedirect(xarModURL('crispbb', 'admin', 'view'));

    return true;
}
?>
