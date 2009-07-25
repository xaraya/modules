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
 * Standard function to re-order forums
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 * @throws none
 */
function crispbb_admin_reorder($args)
{
    if (!xarVarFetch('fid', 'id', $fid)) return;
    if (!xarVarFetch('catid', 'id', $catid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('direction', 'enum:up:down', $direction, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if (!xarSecurityCheck('AdminCrispBB')) return;

    if (!xarSecConfirmAuthKey()) return;
    $move = xarModAPIFunc('crispbb', 'user', 'getforum',
        array('fid' => $fid, 'catid' => $catid, 'unkeyed' => true));

    $catid = isset($catid) ? $catid : isset($move['catid']) ? $move['catid'] : NULL;

    $forums = xarModAPIFunc('crispbb', 'user', 'getforums',
        array('catid' => $catid, 'unkeyed' => 1));
    $now = time();
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    if (!empty($forums) && !empty($move) && !empty($direction)) {
        $currentorderid=$move['forder'];
        $currentforumid=$move['fid'];
        // don't assume the value of the order field is contiguous number sequence.... some forums are deleted
        foreach ($forums as $id=>$forum) {
            // some fiddling - array starts with zero ..
            if (($forums[$id]['forder'] == $currentorderid) && strtolower($direction) == 'up') {
                // We need to find the position fid before (less)
                $swapforumid = (int)$forums[$id-1]['fid'];
                $swaporderid = $forums[$id-1]['forder'];
                $swapposition = $id;
                $currentposition= $id+1;
            } elseif (($forums[$id]['forder'] == $currentorderid) && strtolower($direction) == 'down') {
                // We need to find the position fid after (more)
                $swapforumid = (int)$forums[$id+1]['fid'];
                $swaporderid = $forums[$id+1]['forder'];
                $swapposition = $id+2;
                $currentposition= $id+1;
            }
        }

        if (!xarModAPIFunc('crispbb', 'admin', 'update',
            array('fid' => $fid, 'forder' => $swaporderid, 'nohooks' => true))) return;
        if (!xarModAPIFunc('crispbb', 'admin', 'update',
            array('fid' => $swapforumid, 'forder' => $currentorderid, 'nohooks' => true))) return;

    }

    xarResponseRedirect(xarModURL('crispbb', 'admin', 'view'));

    return true;
}
?>
