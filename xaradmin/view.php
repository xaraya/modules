<?php

function xproject_admin_view($args)
{
    extract($args);

//    if (!xarVarFetch('verbose', 'checkbox', $verbose, 0, XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('status', 'str', $status, $status, XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('projecttype', 'str', $projecttype, $projecttype, XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('sortby', 'str', $sortby, '', XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('q', 'str', $q, '', XARVAR_GET_OR_POST)) return;
//    if (!xarVarFetch('inline', 'int', $inline, $inline, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid', 'int', $clientid, $clientid, XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('memberid', 'int', $memberid, $memberid, XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('max_priority', 'int', $max_priority, $max_priority, XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('max_importance', 'int', $max_importance, $max_importance, XARVAR_NOT_REQUIRED)) return;

    $uid = xarUserGetVar('uid');
        
    $draftstatus = xarModGetVar('xproject', 'draftstatus');
    $activestatus = xarModGetVar('xproject', 'activestatus');
    $archivestatus = xarModGetVar('xproject', 'archivestatus');
    
    $data = xarModAPIFunc('xproject', 'admin', 'menu');

    if(isset($verbose)) $data['verbose'] = $verbose;

    $data['authid'] = xarSecGenAuthKey();

//    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');
//    xarModAPILoad('xprojects', 'user');

    if(!$data['memberid']) {
        $items = xarModAPIFunc('xproject', 'user', 'getall',
                                array('startnum' => $startnum,
                                      'status' => $data['status'],
                                      'projecttype' => $data['projecttype'],
                                      'sortby' => $data['sortby'],
                                      'clientid' => $clientid,
                                      'max_priority' => $data['max_priority'],
                                      'max_importance' => $data['max_importance'],
                                      'q' => $data['q'],
                                      'numitems' => xarModGetUserVar('xproject', 'itemsperpage', $uid)));
    } else {
        $items = xarModAPIFunc('xproject', 'user', 'getmemberprojects',
                                array('memberid' => $data['memberid'],
                                      'startnum' => $startnum,
                                      'status' => $data['status'],
                                      'projecttype' => $data['projecttype'],
                                      'sortby' => $data['sortby'],
                                      'clientid' => $clientid,
                                      'max_priority' => $data['max_priority'],
                                      'max_importance' => $data['max_importance'],
                                      'q' => $data['q'],
                                      'numitems' => xarModGetUserVar('xproject', 'itemsperpage', $uid)));
    }

    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $data['items'] = $items;

    if(!$data['memberid']) {
        $data['pager'] = xarTplGetPager($startnum,
            xarModAPIFunc('xproject', 'user', 'countitems',
                        array('status' => $data['status'],
                              'projecttype' => $data['projecttype'],
                              'sortby' => $data['sortby'],
                              'clientid' => $clientid,
                              'max_priority' => $data['max_priority'],
                              'max_importance' => $data['max_importance'],
                              'q' => $data['q'])),
            xarModURL('xproject',
                    'admin',
                    'view',
                    array('startnum' => '%%',
                          'status' => $data['status'],
                          'projecttype' => $data['projecttype'],
                          'sortby' => $data['sortby'],
                          'clientid' => $clientid,
                          'max_priority' => $data['max_priority'],
                          'max_importance' => $data['max_importance'],
                          'q' => $data['q']))
            ."\" onClick=\"return loadContent(this.href,'projectlist')\"",
            xarModGetUserVar('xproject', 'itemsperpage', $uid));
    } else {
        $data['pager'] = xarTplGetPager($startnum,
            xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                        array('status' => $data['status'],
                              'projecttype' => $data['projecttype'],
                              'sortby' => $data['sortby'],
                              'clientid' => $clientid,
                              'memberid' => $data['memberid'],
                              'max_priority' => $data['max_priority'],
                              'max_importance' => $data['max_importance'],
                              'q' => $data['q'])),
            xarModURL('xproject',
                    'admin',
                    'view',
                    array('startnum' => '%%',
                          'status' => $data['status'],
                          'projecttype' => $data['projecttype'],
                          'sortby' => $data['sortby'],
                          'clientid' => $clientid,
                          'memberid' => $data['memberid'],
                          'max_priority' => $data['max_priority'],
                          'max_importance' => $data['max_importance'],
                          'q' => $data['q']))
            ."\" onClick=\"return loadContent(this.href,'projectlist')\"",
            xarModGetUserVar('xproject', 'itemsperpage', $uid));
    }
    
    if($data['memberid'] > 0) { // || $data['mymemberid'] > 0) {

        // get the current user's totals if no other member is selected
//        if($data['memberid'] > 0) $data['mymemberid'] = $data['memberid'];
        
        if(!empty($draftstatus)) {
            $data['ttldraft'] = xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                                                array('status' => $draftstatus,
                                                      'memberid' => $data['memberid']));
        } else {
            $data['ttldraft'] = 0;
        }
        
        if(!empty($activestatus)) {
            $data['ttlactive'] = xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                                                array('status' => $activestatus,
                                                      'memberid' => $data['memberid']));
        } else {
            $data['ttlactive'] = 0;
        }
        
        if(!empty($archivestatus)) {
            $data['ttlarchive'] = xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                                                array('status' => $archivestatus,
                                                      'memberid' => $data['memberid']));
        } else {
            $data['ttlarchive'] = 0;
        }
        
        $data['ttlhold'] = xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                                        array('status' => "Hold",
                                              'memberid' => $data['memberid']));
    } else {
        if(isset($draftstatus)) {
            $data['ttldraft'] = xarModAPIFunc('xproject', 'user', 'countitems',
                                                array('status' => $draftstatus));
        } else {
            $data['ttldraft'] = 0;
        }
        if(isset($activestatus)) {
            $data['ttlactive'] = xarModAPIFunc('xproject', 'user', 'countitems',
                                                array('status' => $activestatus));
        } else {
            $data['ttlactive'] = 0;
        }
        if(isset($archivestatus)) {
            $data['ttlarchive'] = xarModAPIFunc('xproject', 'user', 'countitems',
                                                array('status' => $archivestatus));
        } else {
            $data['ttlarchive'] = 0;
        }
        $data['ttlhold'] = xarModAPIFunc('xproject', 'user', 'countitems',
                                        array('status' => "Hold"));
    }
    
    $data['returnurl'] = xarModURL('xproject',
                                'admin',
                                'view',
                                array('startnum' => '%%',
                                      'status' => $data['status'],
                                      'projecttype' => $data['projecttype'],
                                      'sortby' => $data['sortby'],
                                      'clientid' => $clientid,
                                      'max_priority' => $data['max_priority'],
                                      'max_importance' => $data['max_importance'],
                                      'q' => $data['q'],
                                      'inline' => 1));
    
    $data['authid'] = xarSecGenAuthKey();
//    $data['inline'] = $inline;

//    $data['verbose'] = $verbose;
//    $data['status'] = $status;
//    $data['sortby'] = $sortby;
    $data['clientid'] = $clientid;
//    $data['q'] = $q;
//    $data['memberid'] = $memberid;
//    $data['max_priority'] = $max_priority;
//    $data['max_importance'] = $max_importance;
//    $data['projecttype'] = $projecttype;

    return $data;
}

?>