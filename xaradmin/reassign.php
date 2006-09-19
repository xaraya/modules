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
function xtasks_admin_reassign($args)
{
    extract($args);

    if (!xarVarFetch('taskid',     'id',     $taskid,     $taskid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl',     'str::',     $returnurl,     '',     XARVAR_NOT_REQUIRED)) return;

    if (empty($returnurl)) {
        $returnurl = $_SERVER['HTTP_REFERER'];
    }
    $item = xarModAPIFunc('xtasks',
                         'user',
                         'get',
                         array('taskid' => $taskid));

    if (!isset($task) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXTask', 1, 'Item', "$item[task_name]:All:$taskid")) {
        return;
    }

    $data = array();

    $data['xtasks_objectid'] = xarModGetVar('xtasks', 'xtasks_objectid');

    $data['taskid'] = $item['taskid'];

    $data['mymemberid'] = xarModGetUserVar('xtasks', 'mymemberid');

    $data['returnurl'] = $returnurl;

    $data['authid'] = xarSecGenAuthKey();

    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $item['module'] = 'xtasks';

    $data['item'] = $item;

    return $data;
}

?>