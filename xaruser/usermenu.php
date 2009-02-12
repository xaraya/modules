<?php

function labaffiliate_user_usermenu($args)
{
    extract($args);
    
    if (!xarSecurityCheck('ViewProgram')) return;
    
    if(!xarVarFetch('form','str', $form, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('phase','str', $phase, 'menu', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('programid','isset', $programid, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('affiliateid','isset', $affiliateid, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('program_key','str', $program_key, "", XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('marketing_copy','str', $marketing_copy, "", XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('primaryprogramid','int', $primaryprogramid, 0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('secondaryprogramid','int', $secondaryprogramid, 0, XARVAR_NOT_REQUIRED)) {return;}
    
    $authid = xarSecGenAuthKey('labaffiliate');
    
    switch(strtolower($phase)) {
        case 'menu':
            $tplvars = array('icon' => 'modules/labaffiliate/xarimages/admin.gif');
            
            $data = xarTplModule('labaffiliate','user', 'usermenu_icon', $tplvars);
            break;
            
        case 'form':
            switch($form) {
                case 'edit':
                    $tplvars = array('icon' => 'modules/labaffiliate/xarimages/admin.gif');
                    
                    $tplvars['authid'] = $authid;
                    
                    $affiliateid = xarModAPIFunc('labaffiliate', 'user', 'getmyaffiliateid', array('userid' => xarUserGetVar('uid')));
                    
                    if($affiliateid === false) return;
                    
                    $tplvars['affiliateid'] = $affiliateid;
            
                    $programs = xarModAPIFunc('labaffiliate', 'user', 'getall', array('status' => 'Active'));
                    
                    if($programs === false) return;
                    
                    $programlist = array();
                    foreach($programs as $programinfo) {
                        $membershipdetails = xarModAPIFunc('labaffiliate', 'membership', 'find',
                                                        array('programid' => $programinfo['programid'],
                                                            'affiliateid' => $affiliateid));
                        $programinfo['membershipinfo'] = $membershipdetails;
                        $programlist[] = $programinfo;
                    }
                    
                    $tplvars['programlist'] = $programlist;
                    
                    $affiliateinfo = xarModAPIFunc('labaffiliate', 'affiliate', 'get', array('affiliateid' => $affiliateid));
                    
                    if($affiliateinfo === false) return;
                    
                    if(!empty($marketing_copy)) {
                        
                        if(!xarModAPIFunc('labaffiliate','affiliate','update',
                                            array('affiliateid' => $affiliateid,
                                                'marketing_copy' => $marketing_copy,
                                                'primaryprogramid' => $primaryprogramid,
                                                'secondaryprogramid' => $secondaryprogramid))) { return; }
                    
                        $affiliateinfo = xarModAPIFunc('labaffiliate', 'affiliate', 'get', array('affiliateid' => $affiliateid));
                        
                        if($affiliateinfo === false) return;
                    
                    }
                    
                    $tplvars['affiliateinfo'] = $affiliateinfo;
                    
                    $marketing_copy = xarModGetVar('labaffiliate', 'marketing_copy');
                    
                    if(!empty($affiliateinfo['marketing_copy'])) {
                        $marketing_copy = $affiliateinfo['marketing_copy'];
                    }
                    
                    $tplvars['marketing_copy'] = $marketing_copy;
                    
                    $data = xarTplModule('labaffiliate','user', 'usermenu_edit', $tplvars);
                    break;
            
                case 'program':
                    $tplvars = array('icon' => 'modules/labaffiliate/xarimages/admin.gif');
                    
                    $tplvars['authid'] = $authid;
                    
                    $affiliateid = xarModAPIFunc('labaffiliate', 'user', 'getmyaffiliateid', array('userid' => xarUserGetVar('uid')));
                    
                    if($affiliateid === false) return;
                    
                    $tplvars['affiliateid'] = $affiliateid;
                    
                    $membershipinfo = xarModAPIFunc('labaffiliate', 'membership', 'find', array('programid' => $programid, 'affiliateid' => $affiliateid));
                    
                    if($membershipinfo === false) return;
                    
                    $tplvars['membershipinfo'] = $membershipinfo;
                    
                    $membershipid = $membershipinfo['membershipid'];
                
                    if(!empty($program_key) || !empty($marketing_copy)) {
        
                        if($membershipinfo['membershipid'] == false) {
                            $membershipid = xarModAPIFunc('labaffiliate','membership','create',
                                                        array('programid' => $programid,
                                                            'affiliateid' => $affiliateid,
                                                            'program_key' => $program_key,
                                                            'marketing_copy' => $marketing_copy));
                            if($membershipid == false) return;
                        }
                        
                        if(!xarModAPIFunc('labaffiliate','membership','update',
                                            array('membershipid' => $membershipid,
                                                'programid' => $programid,
                                                'affiliateid' => $affiliateid,
                                                'program_key' => $program_key,
                                                'marketing_copy' => $marketing_copy))) { return; }
                    
                        $membershipinfo = xarModAPIFunc('labaffiliate', 'membership', 'find', array('programid' => $programid, 'affiliateid' => $affiliateid));
                        
                        if($membershipinfo === false) return;
                        
                        $tplvars['membershipinfo'] = $membershipinfo;
                    }
        
                    $programinfo = xarModAPIFunc('labaffiliate', 'user', 'get', array('programid' => $programid));
                    
                    if($programinfo === false) return;
                    
                    $tplvars['programinfo'] = $programinfo;
                    
                    $data = xarTplModule('labaffiliate','user', 'usermenu_program', $tplvars);
                    break;
        
                    
                default:
                    $tplvars = array('icon' => 'modules/labaffiliate/xarimages/admin.gif');
                    
                    $affiliateid = xarModAPIFunc('labaffiliate', 'user', 'getmyaffiliateid', array('userid' => xarUserGetVar('uid')));
                    
                    if($affiliateid === false) return;
                    
                    $tplvars['affiliateid'] = $affiliateid;
                    
                    $affiliateinfo = xarModAPIFunc('labaffiliate', 'affiliate', 'get', array('affiliateid' => $affiliateid));
                    
                    if($affiliateinfo === false) return;
                    
                    $tplvars['affiliateinfo'] = $affiliateinfo;
                    
                    $downlinelist = xarModAPIFunc('labaffiliate', 'affiliate', 'getall', array('uplineid' => $affiliateid));
                    
                    if($downlinelist === false) return;
                    
                    $tplvars['downlinelist'] = $downlinelist;
            
                    $programs = xarModAPIFunc('labaffiliate', 'user', 'getall', array('owneruid' => xarUserGetVar('uid'), 'status' => 'Active'));
                    
                    if($programs === false) return;
                    
                    $programlist = array();
                    foreach($programs as $programinfo) {
                        $membershipdetails = xarModAPIFunc('labaffiliate', 'membership', 'find',
                                                        array('programid' => $programinfo['programid'],
                                                            'affiliateid' => $affiliateid));
                        $programinfo['membershipinfo'] = $membershipdetails;
                        $programlist[] = $programinfo;
                    }
                    
                    $tplvars['programlist'] = $programlist;
                
                    if(!empty($marketing_copy)) {
                        $primaryprogramid = 0;
                        $secondaryprogramid = 0;
                        if(!xarModAPIFunc('labaffiliate','affiliate','update',
                                            array('affiliateid' => $affiliateid,
                                                'primaryprogramid' => $primaryprogramid,
                                                'secondaryprogramid' => $secondaryprogramid,
                                                'marketing_copy' => $marketing_copy))) { return; }
                    
                        $membershipinfo = xarModAPIFunc('labaffiliate', 'membership', 'find', array('programid' => $programid, 'affiliateid' => $affiliateid));
                        
                        if($membershipinfo === false) return;
                        
                        $tplvars['membershipinfo'] = $membershipinfo;
                    }
                    
                    $data = xarTplModule('labaffiliate','user', 'usermenu_form', $tplvars);
                    break;
            }
        
    }
    
    return $data;
}

?>
