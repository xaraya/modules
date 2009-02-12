<?php
function labaffiliate_uplinecaptureblock_init()
{
    return array('defaultuplineid' => 0,
                 'nocache' => 1, // don't cache by default
                 'pageshared' => 1, // but if you do, share across pages
                 'usershared' => 1, // and for group members
                 'cacheexpire' => null);
}

/**
 * Block info array
 */
function labaffiliate_uplinecaptureblock_info()
{
    return array('text_type' => 'uplinecapture',
         'text_type_long' => 'Establish Affiliate Upline',
         'module' => 'labaffiliate',
         'func_update' => 'labaffiliate_uplinecaptureblock_update',
         'allow_multiple' => false,
         'defaultuplineid' => 0,
         'form_content' => false,
         'form_refresh' => false,
         'show_preview' => false);
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function labaffiliate_uplinecaptureblock_display($blockinfo)
{
    if (!xarVarFetch('affiliateid', 'id', $affiliateid, "", XARVAR_NOT_REQUIRED)) return;
    
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    
    $stored_affiliateid = xarSessionGetVar('affiliateid');
    
    if(empty($affiliateid) && !empty($stored_affiliateid)) {
        $affiliateid = $stored_affiliateid;
    }
    
    $vars['myaffiliateid'] = 0; // default, find or create later
    
    $vars['affiliateid'] = $affiliateid;
        
    if($affiliateid > 0) {
        $affiliateinfo = xarModAPIFunc('labaffiliate','affiliate','get',array('affiliateid' => $affiliateid));
        
        if($affiliateinfo == false) return;
    }
    
    if(xarUserIsLoggedIn()) {
    
        $userid = xarUserGetVar('uid');
    
        $myaffiliateid = xarModAPIFunc('labaffiliate','affiliate','getaffiliateid', array('userid' => $userid));
            
        if($myaffiliateid === false) return;
        
        if($myaffiliateid == 0) {
            $myaffiliateid = xarModAPIFunc('labaffiliate','affiliate','create',
                                            array('uplineid' => $affiliateid,
                    							'userid' => $userid,
                    							'primaryprogramid' => "0",
                    							'secondaryprogramid' => "0"));
            if($myaffiliateid == false) return;     
        }
    
        $vars['myaffiliateid'] = $myaffiliateid;
        
    } elseif($affiliateid > 0) {
        xarSessionSetVar('affiliateid', $affiliateid);
    }
    
    $blockinfo['content'] = $vars;
    
    return $blockinfo;
}

/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function labaffiliate_uplinecaptureblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    
    if(isset($vars['defaultuplineid'])) {
        $blockinfo['content'] = $vars['defaultuplineid'];
    } else {
        $blockinfo['content'] = 0;
    }
    
    $vars['blockid'] = $blockinfo['bid'];
    $vars['defaultuplineid'] = $blockinfo['content'];
    
    return $vars;
}

function labaffiliate_uplinecaptureblock_update($blockinfo)
{
    if (!xarVarFetch('defaultuplineid', 'int::', $vars['defaultuplineid'], 0, XARVAR_NOT_REQUIRED)) {return;}
    
    $blockinfo['content'] = $vars;
    
    return $blockinfo;
}
?>