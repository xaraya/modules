<?php
/**
 * File: $Id$
 *
 * Pubsub User Interface
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
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
    if (!xarVarFetch('modid',      'int::',$modid,FALSE)) return;
    if (!xarVarFetch('cid',         'int::',$cid,FALSE)) return;
    if (!xarVarFetch('itemtype', 'int::',$itemtype,FALSE)) return;
    if (!xarVarFetch('returnurl','str::',$returnurl,FALSE)) return;
    if (!xarVarFetch('subaction','int::',$subaction,FALSE)) return;
    if (!xarVarFetch('userid',   'int::',$userid,FALSE)) return;
    // What is groupdescr???
    if (!xarVarFetch('groupdescr',   'str::',$groupdescr,'Subscribe')) return;

    $returnurl = rawurldecode($returnurl);

    switch ($subaction) {
        case 0:
            xarModAPIFunc('pubsub','user','unsubscribe',
                          array('modid'   =>$modid
                               ,'cid'     =>$cid
                               ,'itemtype'=>$itemtype
                               ,'userid'  =>$userid
                               ));
            break; 
        case 1:
            xarModAPIFunc('pubsub','user','subscribe',
                          array('modid'   =>$modid
                               ,'cid'     =>$cid
                               ,'groupdescr'=>$groupdescr
                               ,'itemtype'=>$itemtype
                               ,'userid'  =>$userid
                               ));
            break; 
        default:
            // do nothing
            break;
    } // end switch 

    xarResponseRedirect($returnurl);
    return true;

} // END modifysubscription

?>
