<?php

/**
 * show the change log for a module item
 */
function changelog_admin_showlog($args)
{ 
    extract($args);

    if (!xarVarFetch('modid',    'isset', $modid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid',   'isset', $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('ReadChangeLog',1,'Item',"$modid:$itemtype:$itemid")) return;

    $data = array();
    $data['changes'] = xarModAPIFunc('changelog','admin','getchanges',
                                     array('modid' => $modid,
                                           'itemtype' => $itemtype,
                                           'itemid' => $itemid));
    if (empty($data['changes']) || !is_array($data['changes'])) return;

    if (xarSecurityCheck('AdminChangeLog',0)) {
        $data['showhost'] = 1;
    } else {
        $data['showhost'] = 0;
    }
    $numchanges = count($data['changes']);
    foreach (array_keys($data['changes']) as $logid) {
        $data['changes'][$logid]['profile'] = xarModURL('roles','user','display',
                                                        array('uid' => $data['changes'][$logid]['editor']));
        if (!$data['showhost']) {
            $data['changes'][$logid]['hostname'] = '';
            $data['changes'][$logid]['link'] = '';
        } else {
            $data['changes'][$logid]['link'] = xarModURL('changelog','admin','showversion',
                                                         array('modid' => $modid,
                                                               'itemtype' => $itemtype,
                                                               'itemid' => $itemid,
                                                               'logid' => $logid));
        }
        if (!empty($data['changes'][$logid]['remark'])) {
            $data['changes'][$logid]['remark'] = xarVarPrepForDisplay($data['changes'][$logid]['remark']);
        }
    // TODO: adapt to local/user time !
        $data['changes'][$logid]['date'] = strftime('%a, %d %B %Y %H:%M:%S %Z', $data['changes'][$logid]['date']);
        // descending order of changes here
        $data['changes'][$logid]['version'] = $numchanges;
        $numchanges--;
    }
    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    $data['itemid'] = $itemid;
    return $data;
}

?>
