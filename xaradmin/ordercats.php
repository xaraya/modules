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
 */
/**
 * Function to do something
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * What this function does
 *
 * @return array
 */
function crispbb_admin_ordercats($args)
{
    if (!xarSecurityCheck('AdminCrispBB', 0) || !xarSecurityCheck('ManageCategories', 0)) {
         return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));
    }
    extract($args);
    if (!xarVarFetch('itemid', 'int:1', $itemid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('direction', 'pre:trim:lower:enum:up:down', $direction, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'pre:trim:lower:str:1', $return_url, '', XARVAR_NOT_REQUIRED)) return;
    $basecats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'crispbb'));
    $basecid = count($basecats) > 0 ? $basecats[0]['category_id'] : null;
    $categories = xarMod::apiFunc('categories', 'user', 'getchildren', array('cids' => array($basecid)));
    $cids = array_keys($categories);
    if (empty($itemid) || !in_array($itemid, $cids)) $invalid[] = 'itemid';
    if (empty($direction)) $invalid[] = 'direction';
    if (!empty($invalid)) {
        $msg = 'Invalid #(1) for #(2) function #(3) in module #(4)';
        $vars = array(join(', ', $invalid), 'admin', 'ordercats', 'crispBB');
        throw new BadParameterException($vars, $msg);
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) {
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
    }
    $cids = array($itemid);
    $refs = array();
    $i = 0;
    foreach ($categories as $refcat => $catinfo) {
        $refs[$i] = $catinfo;
        $i++;
    }

    foreach ($refs as $k => $cat) {
        if ($cat['cid'] == $itemid) {
            if ($direction == 'up') {
                if (isset($refs[$k-1])) {
                    $refcid[$itemid] = $refs[$k-1]['cid'];
                    $position[$itemid] = 1;
                }
            } elseif ($direction == 'down') {
                if (isset($refs[$k+1])) {
                    $refcid[$itemid] = $refs[$k+1]['cid'];
                    $position[$itemid] = 2;
                }
            }
            $name[$itemid] = $cat['name'];
            $description[$itemid] = $cat['description'];
            $image[$itemid] = $cat['image'];
            break;
        }
    }

    $moving[$itemid] = true;
    $catexists[$itemid] = true;
    $repeat = 1;
    $creating = false;
    if (!xarVarFetch('refcid', 'list:int:0', $refcid)) return;
    if (!xarVarFetch('position', 'list:enum:1:2:3:4', $position)) return;

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
            if (!xarMod::apiFunc('categories', 'admin', 'updatecat',
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
    if (empty($return_url)) {
        $return_url = xarServer::getVar('HTTP_REFERER');
    }
    xarResponse::Redirect($return_url);
    return true;
}
?>