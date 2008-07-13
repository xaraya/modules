<?php
/**
 * XTask Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XTask Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_user_view($args)
{
    extract($args);
    
    if (!xarVarFetch('startnum',   'int:1:', $startnum,   1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mymemberid',   'int', $mymemberid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid',   'int', $memberid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('statusfilter',   'str', $statusfilter,   '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xtasks','admin','menu');

    $data['items'] = array();

    if (!xarSecurityCheck('ViewXTask')) {
        return;
    }

    $xtasks = xarModAPIFunc('xtasks',
                          'user',
                          'getall',
                          array('mymemberid' => $mymemberid,
                                'memberid' => $memberid,
                                'statusfilter' => $statusfilter,
                                'startnum' => $startnum,
                                'numitems' => 10));//TODO: numitems

    if (!isset($xtasks) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    for ($i = 0; $i < count($xtasks); $i++) {
        $taskinfo = $xtasks[$i];
        if (xarSecurityCheck('ReadXTask', 0, 'Item', "$taskinfo[task_name]:All:$taskinfo[taskid]")) {
            $xtasks[$i]['link'] = xarModURL('xtasks',
                                               'admin',
                                               'display',
                                               array('taskid' => $taskinfo['taskid']));
        }
        if (xarSecurityCheck('EditXTask', 0, 'Item', "$taskinfo[task_name]:All:$taskinfo[taskid]")) {
            $xtasks[$i]['editurl'] = xarModURL('xtasks',
                                               'admin',
                                               'modify',
                                               array('taskid' => $taskinfo['taskid']));
        } else {
            $xtasks[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteXTask', 0, 'Item', "$taskinfo[task_name]:All:$taskinfo[taskid]")) {
            $xtasks[$i]['deleteurl'] = xarModURL('xtasks',
                                               'admin',
                                               'delete',
                                               array('taskid' => $taskinfo['taskid']));
        } else {
            $xtasks[$i]['deleteurl'] = '';
        }
    }

    $data['xtasks'] = $xtasks;
    $data['pager'] = '';
    return $data;
}

?>
