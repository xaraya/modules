<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_admin_main($args)
{
    extract($args);
    
    $draftstatus = xarModGetVar('xproject', 'draftstatus');
    $activestatus = xarModGetVar('xproject', 'activestatus');
    $archivestatus = xarModGetVar('xproject', 'archivestatus');

    if (!xarVarFetch('verbose', 'checkbox', $verbose, $verbose, XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str', $sortby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q', 'str', $q, '', XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('clientid', 'int', $clientid, $clientid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid', 'int', $memberid, $memberid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_priority', 'int', $max_priority, $max_priority, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_importance', 'int', $max_importance, $max_importance, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projecttype', 'str', $projecttype, $projecttype, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('ViewXProject', 1, 'Item', "All:All:All")) {
        return;
    }

    $uid = xarUserGetVar('uid');
    
    $data = xarModAPIFunc('xproject', 'admin', 'menu');

    $args = array_merge($args, $data);
    
    $data['showsearch'] = 1;

    if(!$memberid) {
        $items = xarModAPIFunc('xproject', 'user', 'getall',
                                array('startnum' => $startnum,
                                      'status' => $status,
                                      'sortby' => $sortby,
                                      'clientid' => $clientid,
                                      'max_priority' => $max_priority,
                                      'max_importance' => $max_importance,
                                      'q' => $q,
                                      'numitems' => xarModGetVar('xproject','itemsperpage')));
    } else {
        $items = xarModAPIFunc('xproject', 'user', 'getmemberprojects',
                                array('memberid' => $args['memberid'],
                                      'startnum' => $startnum,
                                      'status' => $status,
                                      'sortby' => $sortby,
                                      'clientid' => $clientid,
                                      'max_priority' => $max_priority,
                                      'max_importance' => $max_importance,
                                      'q' => $q,
                                      'numitems' => xarModGetVar('xproject','itemsperpage')));
    }

    $args['items'] = $items;
    
    if($memberid > 0) {
        
        if(!empty($draftstatus)) {
            $args['ttldraft'] = xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                                                array('status' => $draftstatus,
                                                      'memberid' => $memberid));
        }
        
        if(!empty($activestatus)) {
            $args['ttlactive'] = xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                                                array('status' => $activestatus,
                                                      'memberid' => $memberid));
        }
        
        if(!empty($archivestatus)) {
            $args['ttlarchive'] = xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                                                array('status' => $archivestatus,
                                                      'memberid' => $memberid));
        }
        
        $args['ttlhold'] = xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                                            array('status' => "Hold",
                                                  'memberid' => $memberid));
    } else {
        $args['ttldraft'] = 0;
        $args['ttlactive'] = 0;
        $args['ttlarchive'] = 0;
        $args['ttlhold'] = 0;
        
        if(!empty($draftstatus)) {
            $args['ttldraft'] = xarModAPIFunc('xproject', 'user', 'countitems',
                                                array('status' => $draftstatus));
        }
        
        if(!empty($activestatus)) {
            $args['ttlactive'] = xarModAPIFunc('xproject', 'user', 'countitems',
                                                array('status' => $activestatus));
        }
        
        if(!empty($archivestatus)) {
            $args['ttlarchive'] = xarModAPIFunc('xproject', 'user', 'countitems',
                                                array('status' => $archivestatus));
        }
        
        $args['ttlhold'] = xarModAPIFunc('xproject', 'user', 'countitems',
                                            array('status' => "Hold"));
    
    }
        
    $args['returnurl'] = xarModURL('xproject',
                                'admin',
                                'view',
                                array('startnum' => '%%',
                                      'status' => $status,
                                      'sortby' => $sortby,
                                      'clientid' => $clientid,
                                      'max_priority' => $max_priority,
                                      'max_importance' => $max_importance,
                                      'projecttype' => $projecttype,
                                      'memberid' => $memberid,
                                      'mymemberid' => $data['mymemberid'],
                                      'q' => $q));

    $args['authid'] = xarSecGenAuthKey();
    $args['inline'] = 1;

    if(!$memberid) {
        $args['pager'] = xarTplGetPager($startnum,
            xarModAPIFunc('xproject', 'user', 'countitems',
                        array('status' => $status,
                              'sortby' => $sortby,
                              'clientid' => $clientid,
                              'max_priority' => $max_priority,
                              'max_importance' => $max_importance,
                              'projecttype' => $projecttype,
                              'q' => $q)),
            xarModURL('xproject', 'admin', 'view', array('startnum' => '%%'))
            ."\" onClick=\"return loadContent(this.href,'projectlist')\"",
            xarModGetUserVar('xproject', 'itemsperpage', $uid));
    } else {
        $args['pager'] = xarTplGetPager($startnum,
            xarModAPIFunc('xproject', 'user', 'countmemberprojects',
                        array('status' => $status,
                              'sortby' => $sortby,
                              'clientid' => $clientid,
                              'memberid' => $memberid,
                              'mymemberid' => $data['mymemberid'],
                              'max_priority' => $max_priority,
                              'max_importance' => $max_importance,
                              'projecttype' => $projecttype,
                              'q' => $q)),
            xarModURL('xproject',
                    'admin',
                    'view',
                    array('startnum' => '%%',
                          'status' => $status,
                          'sortby' => $sortby,
                          'clientid' => $clientid,
                          'memberid' => $memberid,
                          'mymemberid' => $data['mymemberid'],
                          'max_priority' => $max_priority,
                          'max_importance' => $max_importance,
                          'projecttype' => $projecttype,
                          'q' => $q))
            ."\" onClick=\"return loadContent(this.href,'projectlist')\"",
            xarModGetUserVar('xproject', 'itemsperpage', $uid));
    }

    $data['projectlist'] = xarTplModule('xproject',
                                        'admin',
                                        'view',
                                        $args);

    return $data;
}

?>