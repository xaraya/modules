<?php
/**
    Display Ticket

    Display the selected Ticket

    @author  Brian McGilligan bmcgilligan@abrasiontechnology.com
    @access  public / private / protected
    @param   
    @param   
    @return  template
    @throws  list of exception identifiers which can be thrown
    @todo    <Brian McGilligan> ;  
*/ 
function helpdesk_user_display($args)
{
    // Verify that required field is set
    xarVarFetch('ticket_id', 'int:1:',  $ticket_id, null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('tid',       'int:1:',  $ticket_id, null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('activity',  'str:1:',  $activity,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('userid',    'str:1:',  $userid,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',  'int',     $itemtype,  1,     XARVAR_NOT_REQUIRED);
   
    if (empty($ticket_id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'ticket id', 'user', 'viewticket', 'helpdesk');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
    
    $EditAccess     = xarSecurityCheck('edithelpdesk', 0);
    $UserLoggedIn   = xarUserIsLoggedIn();
    $enforceauthkey = xarModGetVar('helpdesk', 'EnforceAuthKey');

    // Load the API
    if (!xarModAPILoad('helpdesk', 'user')) {
        return false;
    }

    // Get User information if it wasn't passed in as a variable
    if(empty($userid)){
        $username = xarUserGetVar('uname');
        $userid   = xarUserGetVar('uid');
    }

    // Verify that the user is either the owner or has at least EDIT access
    // Security check
    // If you dont have EDIT access or
    // if you are NOT the ticket owner, display error

    $isticketowner = xarModAPIFunc('helpdesk', 
                                   'user', 
                                   'ticketowner', 
                                   array('ticket_id' => $ticket_id, 
                                         'userid'    => $userid));
    
    if (($isticketowner == '0') && (!$EditAccess)) {
        $msg = xarML('Illegal Access - You are not allowed to be here!');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
    
    $enabledimages = xarModGetVar('helpdesk', 'Enable Images');
    // Get the ticket Data:
    $data = xarModAPIFunc('helpdesk', 'user', 'getticket', 
                          array('tid'=>$ticket_id)
                         );

    $returnurl = xarModURL('helpdesk', 'user', 'display', array('tid' => $ticket_id));

    /* Get the path to the Cats. */
    $cidlist =  xarModGetVar('helpdesk','mastercids.1');

    $cats = array();
    if(sizeOf($cidlist) > 0 && !empty($cidlist)){
	$mastercids = explode(';',$cidlist);
	foreach($data['categories'] as $category){
	    $cats[] = xarModAPIFunc('helpdesk', 'user', 'cats', 
				    array('cids' => $mastercids,
					  'tcid' => $category['cid'])
				   );
	}    
    }
    $data['cats'] = $cats;  
    
    // Get Comments for the Ticket    
    $data['comments'] = xarModAPIFunc('helpdesk', 'user', 'getcomments', 
                                      array('tid' => $ticket_id,
                                            'returnurl' => $returnurl
                                           )
                                     );
    $item = array();
    $item['module'] = 'helpdesk';
    $item['itemtype'] = $itemtype;
    $item['returnurl'] = $returnurl;
    $hooks = xarModCallHooks('item', 'display', $ticket_id, $item);
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    }else {
        $data['hookoutput'] = $hooks;
    }
                                     
                                             
    $data['menu'] = xarModFunc('helpdesk', 'user', 'menu');    
    $data['summary'] = xarModFunc('helpdesk', 'user', 'summaryfooter');    
    
    return xarTplModule('helpdesk', 'user', 'display', $data);
}
?>
