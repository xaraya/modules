<?php
/**
 * File: $Id$
 * 
 * View a list of topics in a forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_admin_checkip()
{
    // Get parameters from whatever input we need
    if(!xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('ip', 'str', $ip)) return;
    // Security Check PROLLY Not good enough as is.
    if(!xarSecurityCheck('AdminxarBB')) return;

    $data['items'] = array();
    // The user API function is called

    $data['message'] = xarML('Your topics');

    $topics = xarModAPIFunc('xarbb',
                            'user',
                            'getalltopics_byip',
                            array('ip' => $ip,
                                  'startnum' => $startnumitem,
                                  'numitems' => xarModGetVar('xarbb', 'topicsperpage')));
    $topics = array_unique($topics);
    $replies = xarModAPIFunc('xarbb',
                             'user',
                             'getallreplies_byip',
                            array('modid'    => xarModGetIDFromName('xarbb'),
                                  'hostname' => $ip,
                                  'startnum' => $startnumitem,
                                  'numitems' => xarModGetVar('xarbb', 'topicsperpage')));
    $replies = array_unique($replies);
    $results = array_merge($topics, $replies);

    $totalresults=count($results);
    for ($i = 0; $i < $totalresults; $i++) {
        $result = $results[$i];
        if (isset($result['tposter'])){
            $results[$i]['uid'] = xarVarPrepForDisplay($result['tposter']);
        } elseif (isset($result['xar_uid'])){
            $results[$i]['uid'] = xarVarPrepForDisplay($result['xar_uid']);
        }
        //} elseif (isset($result['thostname'])){
        //    $results[$i]['ip'] = xarVarPrepForDisplay($result['thostname']);
        //} elseif (isset($result['xar_hostname'])){
        //   $results[$i]['ip'] = xarVarPrepForDisplay($result['xar_hostname']);
        //}

        if (isset($result['tposter'])){
            $getname = xarModAPIFunc('roles',
                                     'user',
                                     'get',
                                     array('uid' => $result['tposter']));
        } else {
            $getname = xarModAPIFunc('roles',
                                     'user',
                                     'get',
                                     array('uid' => $result['xar_uid']));
        }

        $results[$i]['name'] = $getname['name'];

    }
    $data['items'] = $results;
    $data['ip']     = $ip;
    //var_dump($results); return;
    
    return $data; 
}
?>