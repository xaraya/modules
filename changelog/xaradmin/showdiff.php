<?php

/**
 * show the differences between 2 versions of a module item
 */
function changelog_admin_showdiff($args)
{ 
    extract($args);

    if (!xarVarFetch('modid',    'isset', $modid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid',   'isset', $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}
// Note : this is an array here
    if (!xarVarFetch('logids',    'isset', $logids,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('ReadChangeLog',1,'Item',"$modid:$itemtype:$itemid")) return;

    $data = array();
    $data['diffs'] = xarModAPIFunc('changelog','admin','getdiffs',
                                   array('modid' => $modid,
                                         'itemtype' => $itemtype,
                                         'itemid' => $itemid,
                                         'logids' => $logids));
    if (empty($data['diffs']) || !is_array($data['diffs'])) return;

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

    return $data;
}

?>
