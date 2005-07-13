<?php

function xproject_admin_modifyconfig()
{
	xarModLoad('xproject','user');
	$data = xarModAPIFunc('xproject','user','menu');
	
    if (!xarSecAuthAction(0, 'xproject::', '::', ACCESS_ADMIN)) {
        $msg = xarML('Not authorized to modify #(1) configuration settings',
                               'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
	
	$data['authid'] = xarSecGenAuthKey();

    $dateoptions = array();    
	$dateformatlist = $data['dateformatlist'];
/*
	$x = 0;
	foreach($dateformatlist as $format) {
		$dateoptions[] = array('id'=>$x++,'name'=>strftime($format, time()),'selected'=>"");
	}
*/
	for ($x = 0; $x < count($dateformatlist) ; $x++) {
		$dateoptions[] = array('id'=>$x,'name'=>strftime($dateformatlist[$x], time()),'selected'=>"");
	}
	
	
	
	$data['dateoptions'] = $dateoptions;
    $data['dateformat'] = xarModGetVar('xproject', 'dateformat');

    $data['maxdone'] = xarModGetVar('xproject', 'maxdone');
    $data['refreshmain'] = xarModGetVar('xproject', 'refreshmain');
    $data['showextraasterisk'] = xarModGetVar('xproject','showextraasterisk');
    $data['showlinenumbers'] = xarModGetVar('xproject','showlinenumbers');
    $data['showpercent'] = xarModGetVar('xproject','showpercent');
    $data['showpriority'] = xarModGetVar('xproject','showpriority');
    $data['todoheading'] = xarModGetVar('xproject', 'todoheading');
    $data['itemsperpage'] = xarModGetVar('xproject', 'itemsperpage');
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));

    $hooks = xarModCallHooks('module', 'modifyconfig', 'example',
                            array('module' => 'example'));
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
