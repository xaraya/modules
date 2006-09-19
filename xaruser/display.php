<?php
/**
 * xTasks Module - Project ToDo management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_user_display($args)
{
    extract($args);
    if (!xarVarFetch('taskid', 'id', $taskid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mode', 'str', $mode, '', XARVAR_NOT_REQUIRED)) return;

    $data['xtasks_objectid'] = xarModGetVar('xtask', 'xtasks_objectid');

    if (!xarModAPILoad('xtasks', 'user')) return;

    if (!empty($objectid)) {
        $taskid = $objectid;
    }

    $data = xarModAPIFunc('xtasks','admin','menu');
    $data['taskid'] = $taskid;
    $data['status'] = '';
    $data['mode'] = $mode;

    $item = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('taskid' => $taskid));

    if (!isset($item)) {
        $msg = xarML('Not authorized to access #(1) items',
                    'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return $msg;
    }

    if (xarSecurityCheck('EditXTask', 0, 'Item', "$item[task_name]:All:$item[taskid]")) {
        $item['editurl'] = xarModURL('xtasks',
            'admin',
            'modify',
            array('taskid' => $item['taskid']));
    } else {
        $item['editurl'] = '';
    }
    if (xarSecurityCheck('DeleteXTask', 0, 'Item', "$item[task_name]:All:$item[taskid]")) {
        $item['deleteurl'] = xarModURL('xtasks',
            'admin',
            'delete',
            array('taskid' => $item['taskid']));
    } else {
        $item['deleteurl'] = '';
    }

    list($item['task_name']) = xarModCallHooks('item',
                                         'transform',
                                         $item['taskid'],
                                         array($item['task_name']));

    $data['item'] = $item;
    $data['authid'] = xarSecGenAuthKey();
    $data['task_name'] = $item['task_name'];
    $data['description'] = $item['description'];

    $data['parentid'] = "";
    $data['parentname'] = "";
    $data['parenturl'] = "";
    if($item['parentid'] > 0) {
        $parentinfo = xarModAPIFunc('xtasks',
                              'user',
                              'get',
                              array('taskid' => $item['parentid']));

        if (!isset($parentinfo)) {
            $msg = xarML('Not authorized to access #(1) item parent',
                        'xtasks');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                           new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
            return $msg;
        }

        $data['parentid'] = $parentinfo['taskid'];
        $data['parentname'] = $parentinfo['task_name'];
        $data['parenturl'] = xarModURL('xtasks', 'admin', 'display', array('taskid' => $data['parentid']));
    }

    $worklog = xarModAPIFunc('xtasks', 'worklog', 'getallfromtask', array('taskid' => $item['taskid']));

    $data['worklog'] = $worklog;

    $modid = xarModGetIDFromName(xarModGetName());
    $data['modid'] = $modid;
    $data['itemtype'] = 1;
    $data['objectid'] = $taskid;

    $hooks = xarModCallHooks('item',
                             'display',
                             $taskid,
                             array('module'    => 'xtasks',
                                   'returnurl' => xarModURL('xtasks',
                                                           'admin',
                                                           'display',
                                                           array('taskid' => $taskid))
                                  ),
                            'xtasks');

    if (empty($hooks)) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
}
?>