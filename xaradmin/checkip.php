<?php

/**
 * View an IP by poster
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/

function xarbb_admin_checkip()
{
    if(!xarSecurityCheck('AdminxarBB')) return; 

    if(!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('ip', 'str:1:20', $ip)) return;

    // FIXME: we actually want to retrieve all posters of topics and replies
    // by IP address, *not* the topics and replies themselves.
    // This can be much more efficiently done in a new API that checks for
    // uniqueness (and can can count posts) in one go.

    $data['items'] = array();
    $data['message'] = xarML('Your topics');
    $topics = xarModAPIFunc('xarbb', 'user', 'getalltopics_byip',
        array(
            'ip' => $ip,
            'startnum' => $startnum,
            'numitems' => xarModGetVar('xarbb', 'topicsperpage')
        )
    );
    $topics = array_unique($topics);

    $replies = xarModAPIFunc('xarbb', 'user', 'getallreplies_byip',
        array(
            'modid'    => xarModGetIDFromName('xarbb'),
            'hostname' => $ip,
            'startnum' => $startnum,
            'numitems' => xarModGetVar('xarbb', 'topicsperpage')
        )
    );
    $replies = array_unique($replies);

    $results = array_merge($topics, $replies);

    $totalresults = count($results);
    for ($i = 0; $i < $totalresults; $i++) {
        $result = $results[$i];
        if (isset($result['tposter'])){
            $results[$i]['uid'] = xarVarPrepForDisplay($result['tposter']);
        } elseif (isset($result['xar_uid'])){
            $results[$i]['uid'] = xarVarPrepForDisplay($result['xar_uid']);
        }
        if (isset($result['tposter'])){
            $getname = xarModAPIFunc('roles', 'user', 'get', array('uid' => $result['tposter']));
        } else {
            $getname = xarModAPIFunc('roles', 'user', 'get', array('uid' => $result['xar_uid']));
        }
        $results[$i]['name'] = $getname['name'];
    }

    $data['items'] = $results;
    $data['ip'] = $ip;

    return $data; 
}

?>