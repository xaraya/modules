<?php
/**
  Creates a new ticket
  
  @author Brian McGilligan
  @returns A new ticket form
*/
function helpdesk_user_new()
{         
    // Some of these values get used more than once in this procedure.
    // Make the call to get their value here to prevent multiple function calls
    // and/or db queries
    $data['allowusercheckstatus']  = xarModGetVar('helpdesk', 'User can check status');
    $data['allowusersubmitticket'] = xarModGetVar('helpdesk', 'User can Submit');
    $data['allowanonsubmitticket'] = xarModGetVar('helpdesk', 'Anonymous can Submit');
    $data['allowcloseonsubmit']    = xarModGetVar('helpdesk', 'AllowCloseOnSubmit');
    $data['readaccess']            = xarSecurityCheck('readhelpdesk', 0);
    $data['editaccess']            = xarSecurityCheck('edithelpdesk', 0);
    $data['adminaccess']           = xarSecurityCheck('adminhelpdesk', 0);
    $data['openedbydefaulttologgedin']   = xarModGetVar('helpdesk', 'OpenedByDefaultToLoggedIn');
    $data['assignedtodefaulttologgedin'] = xarModGetVar('helpdesk', 'AssignedToDefaultToLoggedIn');
    $data['userisloggedin']        = xarUserIsLoggedIn();
       
    $data['menu'] = xarModFunc('helpdesk', 'user', 'menu');
    $data['enabledimages'] = xarModGetVar('helpdesk', 'Enable Images');
    
    // Security check
    // No need for a security check if Anonymous Adding is enabled:
    // So ONLY check security if AllowAnonAdd is NOT TRUE
    if (!$data['allowanonsubmitticket']){	
        if (!xarSecurityCheck('readhelpdesk')) return;
    }
    
    if (!xarVarFetch('itemtype', 'int', $itemtype, 1, XARVAR_NOT_REQUIRED)) return;
    
    $data['username'] = xarUserGetVar('uname');
    $data['name']     = xarUserGetVar('name');
    $data['userid']   = xarUserGetVar('uid');
    
    if($data['userisloggedin']){
    $data['email']    = xarUserGetVar('email');        
    $data['phone']    = ""; //xarUserGetVar('phone');        
    }
    else{
    $data['email']    = "";        
    $data['phone']    = ""; //xarUserGetVar('phone');                
    }
    
    /*
    * These funcs should be rethought once we get the rest working
    */
    $data['priority'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
                                      array('itemtype' => 2));

    $data['sources'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
                                     array('itemtype' => 4));

                                     
    $cidlist =  xarModGetVar('helpdesk','mastercids.1');
    $data['cats'] = xarModAPIFunc('categories', 'visual', 'makeselect',
                                 array('cid' => $cidlist,
                                       'multiple' => false));                      
    
    if($data['editaccess']){                                     
        $data['reps'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
                                      array('itemtype' => 10));
        $data['users'] = xarModAPIFunc('roles', 'user', 'getall');
    }
                                      
                                                 
    $data['enforceauthkey'] = xarModGetVar('helpdesk', 'EnforceAuthKey');
    $data['action']  = xarModURL('helpdesk', 'user', 'create');        
    $data['summary'] = xarModFunc('helpdesk', 'user', 'summaryfooter');

    $item = array();
    $item['module']   = 'helpdesk';
    $item['itemtype'] = $itemtype;
    $item['multiple'] = false;
    $item['returnurl'] = xarModURL('helpdesk', 'user', 'main');
    $hooks = xarModCallHooks('item', 'new', $itemtype, $item, 'helpdesk');
    if (empty($hooks)) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    } 
                 
    return xarTplModule('helpdesk', 'user', 'new', $data);
}
?>
