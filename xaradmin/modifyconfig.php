<?php

function xproject_admin_modifyconfig()
{
    //xarModLoad('xproject','user');
    $data = xarModAPIFunc('xproject','admin','menu');
/*
    if (!xarSecurityCheck('AdminXProject', 0)) {
        return;
    }
*/
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

    $hooks = xarModCallHooks('module', 'modifyconfig', 'xproject',
                       array('module' => 'xproject'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for example module'));
    } else {
        $data['hooks'] = $hooks;

         /* You can use the output from individual hooks in your template too, e.g. with
         * $hooks['categories'], $hooks['dynamicdata'], $hooks['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }
    return $data;
}

?>
