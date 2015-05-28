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
    if (!xarVarFetch('modid',     'int',$modid,FALSE)) return;
    if (!xarVarFetch('cid',       'int',$cid,FALSE)) return;
    if (!xarVarFetch('itemtype',  'int',$itemtype,FALSE)) return;
    if (!xarVarFetch('returnurl', 'str',$returnurl,FALSE)) return;
    if (!xarVarFetch('subaction', 'int',$subaction,FALSE)) return;
    if (!xarVarFetch('extra',     'str',$extra,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('groupdescr','str',$groupdescr,'Subscribe',XARVAR_NOT_REQUIRED)) return;

    $returnurl = rawurldecode($returnurl);

    // the currently logged in user
    $userid = (int)xarUser::getVar('id');
    if ($userid == (int)xarConfigVars::get(null, 'Site.User.AnonymousUID')) {
        xarController::redirect($returnurl);
        return true;
    }

    switch ($subaction) {
        case 0:
            xarMod::apiFunc('pubsub','user','unsubscribe',
                          array('modid'   => $modid
                               ,'itemtype'=> $itemtype
                               ,'cid'     => $cid
                               ,'extra'   => $extra
                               ,'userid'  => $userid
                               ));
            break;
        case 1:
            xarMod::apiFunc('pubsub','user','subscribe',
                          array('modid'     => $modid
                               ,'itemtype'  => $itemtype
                               ,'cid'       => $cid
                               ,'extra'     => $extra
                               ,'groupdescr'=> $groupdescr
                               ,'userid'    => $userid
                               ));
            break;
        default:
            // do nothing
            break;
    } // end switch

    xarController::redirect($returnurl);
    return true;
}

?>
