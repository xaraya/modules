<?php

function xtasks_admin_new($args)
{
    extract($args);
    
    if (!xarVarFetch('showajax', 'str', $showajax, $showajax, XARVAR_NOT_REQUIRED)) return;
    if($showajax) {
        $projectid = 0;
    }
    if (!xarVarFetch('parentid', 'int', $parentid, $parentid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'int', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modid', 'int', $modid, $modid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'int', $itemtype, $itemtype, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    
    xarModLoad('xtasks','user');
    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('AddXTask')) {
        return;
    }
    
    if(!empty($returnurl)) {
        $data['returnurl'] = $returnurl;
    } elseif (empty($data['returnurl'])) {
        $data['returnurl'] = xarServerGetCurrentURL();
    } else {
        $data['returnurl'] = xarModURL('xtasks','admin','view');
    }
    
    
    if(!strpos($data['returnurl'], "mode")) {
        $data['returnurl'] = $data['returnurl']."&amp;mode=tasklist#tasklist";
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['showajax'] = $showajax;
    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    $data['objectid'] = $objectid;
    $data['projectid'] = $projectid;
    
    if($projectid > 0) {
        $projectinfo = xarModAPIFunc('xproject', 'user', 'get', array('projectid' => $projectid));
    } else {
        $projectinfo = array();
    }
    $data['projectinfo'] = $projectinfo;
    
    $data['parentid'] = 0;
    $data['priority'] = 5;
    $data['status'] = "Draft";
    $data['private'] = 1;
    $data['importance'] = 5;
    
    $data['date_start_planned'] = date("Y-m-d");
    
    if($data['modid'] == xarModGetIDFromName('xproject')) {
        $data['projectid'] = $objectid;
    }
    
    if($data['modid'] == xarModGetIDFromName('xtasks')) {
        $data['parentid'] = $objectid;
        $parentinfo = xarModAPIFunc('xtasks', 'user', 'get', array('taskid' => $objectid));
        $data['projectid'] = $parentinfo['projectid'];
        $data['objectid'] = $parentinfo['taskid'];
        $data['modid'] = xarModGetIDFromName('xtasks');
        $data['itemtype'] = 1;
        $data['priority'] = $parentinfo['priority'];
        $data['status'] = $parentinfo['status'];
        $data['private'] = $parentinfo['private'];
        $data['importance'] = $parentinfo['importance'];
        $data['date_end_planned'] = $parentinfo['date_end_planned'];
    }

    if(empty($data['modid'])) $data['modid'] = xarModGetIDFromName('xtasks');
    if(empty($data['itemtype'])) $data['itemtype'] = 1;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));

    $item = array();
    $item['module'] = 'xtasks';
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
