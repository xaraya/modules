<?php

function xtasks_admin_modify($args)
{
	extract($args);
    
    if (!xarVarFetch('taskid',     'id',     $taskid,     $taskid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl',     'str::',     $returnurl,     '',     XARVAR_NOT_REQUIRED)) return;
	
    if (!empty($objectid)) {
        $taskid = $objectid;
    }
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
    
	$data['returnurl'] = $returnurl;
	
    $data['authid'] = xarSecGenAuthKey();
	
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

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