<?php
/**
  Modify a ticket item
  
  @author Brian McGilligan
  @return Template data
*/
function helpdesk_user_modify($args)
{
    extract($args);
    $enforceauthkey = xarModGetVar('helpdesk', 'EnforceAuthKey');
    // Possible formaction values:
    // UPDATE / MODIFY / DELETE / DELETE_VERIFIED
    xarVarFetch('formaction', 'str:1:',  $formaction, null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('tid',        'int:1:',  $tid,        null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('userid',     'int:1:',  $userid,     null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('confirm',    'isset',   $confirm,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',   'int',     $itemtype,   1,     XARVAR_NOT_REQUIRED);
    
    
    $data['EditAccess']     = xarSecurityCheck('edithelpdesk', 0);
    $data['UserLoggedIn']   = xarUserIsLoggedIn();
    $data['enforceauthkey'] = xarModGetVar('helpdesk', 'EnforceAuthKey');
    $data['enabledimages']  = xarModGetVar('helpdesk', 'Enable Images');
    
    if (!xarModAPILoad('helpdesk', 'user')) { return false; }

    $data['menu']    = xarModFunc('helpdesk', 'user', 'menu');
         
    $data['username'] = xarUserGetVar('uname');
    $data['userid']   = xarUserGetVar('uid');
    
    $isticketowner = xarModAPIFunc('helpdesk', 'user', 'ticketowner', 
                                   array('ticket_id' => $tid, 
                                         'userid'    => $userid));

    if ((!$isticketowner) && (!$data['EditAccess'])) {
        $msg = xarML('Illegal Access - You are not allowed to be here!');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
       
    // If we have confirmation do the update
    if(!empty($confirm)){
        $item = array();
        $item['module'] = 'helpdesk';
        $item['itemtype'] = $itemtype;
        $hooks = xarModCallHooks('item', 'update', $tid, $item);
        if (empty($hooks)) {
            $data['hookoutput'] = '';
        }else {
            $data['hookoutput'] = $hooks;
        }
        $updateresult = xarModAPIFunc('helpdesk', 'user', 'update');
        if($data['EditAccess']){ $selection = "MYASSIGNEDALL"; }
        else { $selection = "MYALL"; }
        xarResponseRedirect(xarModURL('helpdesk', 'user', 'view',
                                      array('tid'       => $tid,
                                            'selection' => $selection)
                                     )
                           );
        return true;                           
    }    
    
    // Get the ticket Data:
    $data['ticketdata']   = xarModAPIFunc('helpdesk','user','getticket',
                                          array('tid' => $tid));
    
    /*
    * These funcs should be rethought once we get the rest working
    */
    $data['priority'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
                                      array('itemtype' => 2));

    $data['status'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
                                      array('itemtype' => 3));

    $data['sources'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
                                     array('itemtype' => 4));

    $data['reps'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
                                   array('itemtype' => 10));
        
    $data['users'] = xarModAPIFunc('roles', 'user', 'getall');                  
    
    $item = array();
    $item['module'] = 'helpdesk';
    $item['itemtype'] = $itemtype;
    $hooks = xarModCallHooks('item', 'modify', $tid, $item);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    }else {
        $data['hookoutput'] = $hooks;
    }
    
    $data['tid'] = $tid;
    
    if(empty($hours))   { $data['hours'] = 0; }
    if(empty($minutes)) { $data['minutes'] = 0; }   
    
    $data['summary'] = xarModFunc('helpdesk', 'user', 'summaryfooter');    
        
    return xarTplModule('helpdesk', 'user', 'modify', $data);
    
}
?>
