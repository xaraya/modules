<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
function labaffiliate_user_main()
{    
    if (!xarVarFetch('affiliateid', 'id', $affiliateid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('programid', 'id', $programid, NULL, XARVAR_NOT_REQUIRED)) return;
    
    if (!xarSecurityCheck('ViewProgram')) return;
    
    $data = array();
    
    if(xarUserIsLoggedIn()) {
        $affiliateid = xarModAPIFunc('labaffiliate', 'user', 'getmyaffiliateid', array('userid' => xarUserGetVar('uid')));
        
        if($affiliateid === false) return;
    }
    
    if($affiliateid == false) {
        $affiliateid = xarModGetVar('labaffiliate','defaultaffiliateid');
    }
    
    $data['affiliateid'] = $affiliateid;
    
    $default_marketing_copy = xarModGetVar('labaffiliate','default_marketing_copy');
    
    $displaytitle = xarModGetVar('labaffiliate','displaytitle');
    
    if($displaytitle == false) {
        $displaytitle = xarML('labAffiliate');
    }
    
    $data['displaytitle'] = $displaytitle;
        
    $affiliateinfo = array();
    $programlist = array();
    
    if($affiliateid == true) {
        $affiliateinfo = xarModAPIFunc('labaffiliate', 'affiliate', 'get', array('affiliateid' => $affiliateid));
        
        if($affiliateinfo === false) return;
        
        if(!empty($affiliateinfo['marketing_copy'])) {
            $default_marketing_copy = $affiliateinfo['marketing_copy'];
        }
        
        $programs = xarModAPIFunc('labaffiliate', 'user', 'getall', array('status' => 'Active'));
        
        if($programs === false) return;
        
        foreach($programs as $programinfo) {
            $membershipdetails = xarModAPIFunc('labaffiliate', 'membership', 'find',
                                            array('programid' => $programinfo['programid'],
                                                'affiliateid' => $affiliateid));
            if($membershipdetails === false) return;
            if(!empty($membershipdetails['program_key'])) {
                $programinfo['membershipinfo'] = $membershipdetails;
                $programlist[] = $programinfo;
            }
        }
    }
    
    if($programid) {
        $programinfo = xarModAPIFunc('labaffiliate', 'user', 'get', array('programid' => $programid));
        
        if($programinfo == false) return;
        
        $default_marketing_copy = $programinfo['marketing_copy'];
    
        $membershipinfo = xarModAPIFunc('labaffiliate', 'membership', 'find', array('programid' => $programid, 'affiliateid' => $affiliateid));
        
        if($membershipinfo == false) {
            return;
        } else {
            $default_marketing_copy = $membershipinfo['marketing_copy'];
        }
    }
    
    if($default_marketing_copy == false) {
        $default_marketing_copy = "";
    }
    
    $data['marketing_copy'] = $default_marketing_copy;
    
    $data['affiliateinfo'] = $affiliateinfo;
    
    $data['programlist'] = $programlist;

    return $data;
}

?>