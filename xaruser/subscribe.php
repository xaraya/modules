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

    // Get the topic data
    $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    // If there are subscribers already, we need to update that array
    // else we start a new array.
    if (!empty($data['toptions'])){
        $topicoptions = unserialize($data['toptions']);
        if (!isset($topicoptions['subscribers'])) {
            $topicoptions['subscribers'] = array();
        } elseif (in_array($uid, $topicoptions['subscribers'])) {
            // We're already subscribed
            xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
            return true;
        }
        array_push($topicoptions['subscribers'], $uid);
        $mergedarray = serialize($topicoptions);
    } else {
        $topicoptions['subscribers'] = array($uid);
        $mergedarray = serialize($topicoptions);
    }
    // Then we just need to push the update through.
    if (!xarModAPIFunc('xarbb', 'user', 'updatetopic',
        array(
            'tid'      => $tid,
            'fid'      => $data['fid'],
            'ttime'    => $data['ttime'],
            'toptions' => $mergedarray
        )
    )) return;

    // And then go back to the topic.
    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));

    return true;
}

?>