<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * update a category from categories_admin_modify
 *
 * @param bool reassign (checkbox)
 * @param int repeat
 * @return bool if Function has ended successfully
 */
function categories_admin_updatecat()
{
    // Get parameters

    //Checkbox work for submit buttons too
    if (!xarVarFetch('reassign', 'checkbox',  $reassign, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('repeat',   'int:1:100', $repeat,   1,     XARVAR_NOT_REQUIRED)) return;
    if ($reassign) {
        xarResponseRedirect(xarModUrl('categories','admin','modifycat',array('repeat' => $repeat)));
        return true;
    }
    if (!xarVarFetch('creating', 'bool', $creating)) return;

    if ($creating) {
        if (!xarVarFetch('cids', 'array', $cids)) return;
    } else {
        if (!xarVarFetch('cids', 'array', $cids)) return;
    }
    if (!xarVarFetch('name', 'list:str:0:255', $name)) return;
    if (!xarVarFetch('description', 'list:str:0:255', $description)) return;
    if (!xarVarFetch('image', 'array', $image)) return;
    if (!xarVarFetch('moving', 'list:bool', $moving)) return;
    if (!xarVarFetch('catexists', 'list:bool', $catexists)) return;
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
            if (!xarModAPIFunc('categories',
                             'admin',
                             'updatecat',
                             array('cid'         => $cid,
                                   'name'        => $name[$cid],
                                   'description' => $description[$cid],
                                   'image'       => $image[$cid],
                                   'moving'      => $moving[$cid],
                                   'refcid'      => $refcid[$cid],
                                   'inorout'     => $inorout,
                                   'rightorleft' => $rightorleft
                               ))) return;
        } else {
            // Pass to API
            if (!xarModAPIFunc('categories',
                              'admin',
                              'createcat',
                              array(
                                    'name'        => $name[$cid],
                                    'description' => $description[$cid],
                                    'image'       => $image[$cid],
                                    'catexists'   => $catexists[$cid],
                                    'refcid'      => $refcid[$cid],
                                    'inorout'     => $inorout,
                                    'rightorleft' => $rightorleft
                                   ))) return;
        }
    }
    if ($creating) {
        xarResponseRedirect(xarModUrl('categories','admin','modifycat',array()));
    } else {
        xarResponseRedirect(xarModUrl('categories','admin','viewcats',array()));
    }
    return true;
}
?>
