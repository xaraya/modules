<?php

function xproject_admin_new()
{
	xarModLoad('xproject','user');
	$data = xarModAPIFunc('xproject','user','menu');

    if (!xarSecAuthAction(0, 'xproject::', '::', ACCESS_ADD)) {
        $msg = xarML('Not authorized to access to #(1)',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $data['authid'] = xarSecGenAuthKey();

    $sendmailoptions = array();    
    $sendmailoptions[] = array('id'=>0,'name'=>xarMLBYKey('Please choose an email option'));
    $sendmailoptions[] = array('id'=>1,'name'=>xarMLBYKey("any changes"));
    $sendmailoptions[] = array('id'=>2,'name'=>xarMLBYKey("major changes"));
    $sendmailoptions[] = array('id'=>3,'name'=>xarMLBYKey("weekly summaries"));
    $sendmailoptions[] = array('id'=>4,'name'=>xarMLBYKey("Do NOT send email"));
	$data['sendmailoptions'] = $sendmailoptions;
	
    $data['addbutton'] = xarVarPrepForDisplay(xarMLByKey('Add'));

    $item = array();
    $item['module'] = 'xproject';
    $hooks = xarModCallHooks('item','new','',$item);
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
