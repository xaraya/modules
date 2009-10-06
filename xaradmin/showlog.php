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
    $data['changes'] = xarMod::apiFunc('changelog','admin','getchanges',
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
    $data['numversions'] = $numchanges;
    foreach (array_keys($data['changes']) as $logid) {
        $data['changes'][$logid]['profile'] = xarModURL('roles','user','display',
                                                        array('id' => $data['changes'][$logid]['editor']));
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
        // 2template $data['changes'][$logid]['date'] = xarLocaleFormatDate($data['changes'][$logid]['date']);
        // descending order of changes here
        $data['changes'][$logid]['version'] = $numchanges;
        $numchanges--;
    }
    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    $data['itemid'] = $itemid;

    $logidlist = array_keys($data['changes']);

    if (count($logidlist) > 0) {
        $firstid = $logidlist[count($logidlist)-1];
        $data['prevversion'] = xarModURL('changelog','admin','showversion',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logid' => $firstid));
        if (count($logidlist) > 1) {
            $previd = $logidlist[count($logidlist)-2];
            $data['prevdiff'] = xarModURL('changelog','admin','showdiff',
                                          array('modid' => $modid,
                                                'itemtype' => $itemtype,
                                                'itemid' => $itemid,
                                                'logids' => $firstid.'-'.$previd));
        }
    }
    if (count($logidlist) > 1) {
        $lastid = $logidlist[0];
        $data['nextversion'] = xarModURL('changelog','admin','showversion',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logid' => $lastid));
        if (count($logidlist) > 2) {
            $nextid = $logidlist[1];
            $data['nextdiff'] = xarModURL('changelog','admin','showdiff',
                                          array('modid' => $modid,
                                                'itemtype' => $itemtype,
                                                'itemid' => $itemid,
                                                'logids' => $nextid.'-'.$lastid));
        }
    }

    $modinfo = xarModGetInfo($modid);
    if (empty($modinfo['name'])) {
        return $data;
    }
    $itemlinks = xarMod::apiFunc($modinfo['name'],'user','getitemlinks',
                               array('itemtype' => $itemtype,
                                     'itemids' => array($itemid)),
                               0);
    if (isset($itemlinks[$itemid])) {
        $data['itemlink'] = $itemlinks[$itemid]['url'];
        $data['itemtitle'] = $itemlinks[$itemid]['title'];
        $data['itemlabel'] = $itemlinks[$itemid]['label'];
    }

    return $data;
}

?>
