<?php

/**
 * Subscribe to a topic
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
 * @todo merge with pubsub ?
*/

function xarbb_user_subscribe()
{
    // No anons please
    if (!xarUserIsLoggedIn()) return;

    // And you better have rights.
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;

    // All we need is who and where.
    if (!xarVarFetch('tid', 'id', $tid)) return;

    // Do not allow specifying the uid via URL parameters !
    $uid = (int)xarUserGetVar('uid');

    if (!xarModAPIFunc('xarbb', 'admin', 'subscribe', array('tid'=>$tid, 'uid'=>$uid))) return;

    // And then go back to the topic.
    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));

    return true;
}

?>