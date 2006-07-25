<?php
/**
 * XProject Module - A simple task management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
function xtasks_admin_display($args)
{
    extract($args);
    if (!xarVarFetch('taskid', 'id', $taskid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    $data['xtasks_objectid'] = xarModGetVar('xtask', 'xtasks_objectid');

    if (!xarModAPILoad('xtasks', 'user')) return;

    if (!empty($objectid)) {
        $taskid = $objectid;
    }

    $data = xarModAPIFunc('xtasks','user','menu');
    $data['taskid'] = $taskid;
    $data['status'] = '';

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
    
    list($item['task_name']) = xarModCallHooks('item',
                                         'transform',
                                         $item['taskid'],
                                         array($item['task_name']));
    
    $data['item'] = $item;
    $data['authid'] = xarSecGenAuthKey();
    $data['task_name'] = $item['task_name'];
    $data['description'] = $item['description'];

    $hooks = xarModCallHooks('item',
                             'display',
                             $taskid,
                             xarModURL('xtasks',
                                       'admin',
                                       'display',
                                       array('taskid' => $taskid)));
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

    return $data;
}
?>