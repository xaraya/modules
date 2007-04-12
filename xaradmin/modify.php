<?php

function xtasks_admin_modify($args)
{
    extract($args);
    
    if (!xarVarFetch('taskid',     'id',     $taskid,     $taskid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('inline',     'str::',     $inline,     '',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl',     'str::',     $returnurl,     '',     XARVAR_NOT_REQUIRED)) return;
    
    if (!empty($objectid)) {
        $taskid = $objectid;
    }
    if (empty($returnurl)) {
        $returnurl = xarModURL('xtasks', 'admin', 'display', array('taskid' => $taskid));
    }
    $item = xarModAPIFunc('xtasks',
                         'user',
                         'get',
                         array('taskid' => $taskid));
    
    if (!isset($task) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXTask', 1, 'Item', "$item[task_name]:All:$taskid")) {
        return;
    }
    
    $data = xarModAPIFunc('xtasks', 'admin', 'menu');

    $data['xtasks_objectid'] = xarModGetVar('xtasks', 'xtasks_objectid');
    
    $data['taskid'] = $item['taskid'];
    
    $data['returnurl'] = $returnurl;
    
    $data['inline'] = $inline;
    
    $data['authid'] = xarSecGenAuthKey();
    
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));
    
    $data['cancelbutton'] = xarVarPrepForDisplay(xarML('Cancel'));

    $item['module'] = 'xtasks';

    $data['item'] = $item;

    $hooks = xarModCallHooks('item','modify',$taskid,$item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    
    return $data;
}

?>