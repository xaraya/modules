<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * subscribe user to a pubsub element
 * @param $modid module ID of event
 * @param $cid cid of event category
 * @param $itemtype itemtype of event
 * @param $returnurl page we came from so that we go back there
 * @param $subaction notifies if this is a susbscribe or unsubscribe action
 * @param $groupdesc <garrett>: no idea
 * @return output with pubsub information
 */
function pubsub_user_modifysubscription()
{
    if (!xarVar::fetch('modid', 'int', $modid, false)) {
        return;
    }
    if (!xarVar::fetch('cid', 'int', $cid, false)) {
        return;
    }
    if (!xarVar::fetch('itemtype', 'int', $itemtype, false)) {
        return;
    }
    if (!xarVar::fetch('returnurl', 'str', $returnurl, false)) {
        return;
    }
    if (!xarVar::fetch('subaction', 'int', $subaction, false)) {
        return;
    }
    if (!xarVar::fetch('extra', 'str', $extra, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('groupdescr', 'str', $groupdescr, 'Subscribe', xarVar::NOT_REQUIRED)) {
        return;
    }

    $returnurl = rawurldecode($returnurl);

    // The currently logged in user
    if (!xarUser::isLoggedIn()) {
        xarController::redirect($returnurl);
        return;
    }

    switch ($subaction) {
        case 0:
            xarMod::apiFunc(
                'pubsub',
                'user',
                'unsubscribe',
                ['modid'   => $modid,'itemtype'=> $itemtype,'cid'     => $cid,'extra'   => $extra,'userid'  => $userid,
                               ]
            );
            break;
        case 1:
            xarMod::apiFunc(
                'pubsub',
                'user',
                'subscribe',
                ['modid'     => $modid,'itemtype'  => $itemtype,'cid'       => $cid,'extra'     => $extra,'groupdescr'=> $groupdescr,'userid'    => $userid,
                               ]
            );
            break;
        default:
            // do nothing
            break;
    } // end switch

    xarController::redirect($returnurl);
    return true;
}
