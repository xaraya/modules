<?php

function xtasks_admin_new($args)
{
    extract($args);

    if (!xarVarFetch('inline', 'str', $inline, $inline, XARVAR_NOT_REQUIRED)) return;
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
    $data['returnurl'] = $data['returnurl']."&amp;mode=tasks#tasklist";

    $data['authid'] = xarSecGenAuthKey();
    $data['inline'] = $inline;
    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    $data['objectid'] = $objectid;
    
    $data['parentid'] = "";
    $data['priority'] = xarModGetVar('xtasks', 'defaultpriority');
    $data['status'] = "";
    $data['private'] = "";
    $data['importance'] = xarModGetVar('xtasks', 'defaultimportance');

    if($data['modid'] == xarModGetIDFromName('xtasks') && $data['itemtype'] == 1) {
        $data['parentid'] = $objectid;
        $parentinfo = xarModAPIFunc('xtasks', 'user', 'get', array('taskid' => $objectid));
        $data['priority'] = $parentinfo['priority'];
        $data['status'] = $parentinfo['status'];
        $data['private'] = $parentinfo['private'];
        $data['importance'] = $parentinfo['importance'];
    }

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
