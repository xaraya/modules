<?php
/**
    create an item
    
    @author Brian McGilligan
    @return
*/
function helpdesk_user_create($args)
{
    if (empty($allowanonadd)){
        if (!xarSecurityCheck('readhelpdesk')) return;
    }

    $data['enforceauthkey'] = xarModGetVar('helpdesk', 'EnforceAuthKey');

    if ($data['enforceauthkey']){
        if (!xarSecConfirmAuthKey()) {
            xarResponseRedirect(xarModURL('helpdesk', 'user', 'main'));
            return true;
        }
    }
    // Get some info about the mod and a ticket type id
    $modid = xarModGetIDFromName('helpdesk');
    $itemtype = 1;
    
    // Get parameters from whatever input we need.    
    xarVarFetch('name',     'str:1:',   $name,      null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('userid',   'int:1:',   $userid,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('phone',    'str:1:',   $phone,     null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('email',    'email:1:', $email,     null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('domain',   'str:1:',   $domain,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('subject',  'str:1:',   $subject,   null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('nontech',  'str:1:',   $nontech,   null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('source',   'int:1:',   $source,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('status',   'int:1:',   $status,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('priority', 'int:1:',   $priority,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('sw_id',    'int:1:',   $sw_id,     null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('swv_id',   'int:1:',   $swv_id,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('types',    'int:1:',   $type,      null,  XARVAR_NOT_REQUIRED);    
    xarVarFetch('openedby', 'int:1:',   $openedby,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('assignedto','int:1:',  $assignedto,null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('closedby', 'int:1:',   $closedby,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('issue',    'str:1:',   $issue,     null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('notes',    'str:1:',   $notes,     null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('hours',    'int:1:',   $hours,     null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('minutes',  'int:1:',   $minutes,   null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('cids',     'array',    $cids,      array(),    XARVAR_NOT_REQUIRED);
    xarVarFetch('closeonsubmit', 'int', $closeonsubmit,  null,  XARVAR_NOT_REQUIRED);
    
    if ($nontech || $openedby == 0){
        // If the NONTECHSUBMIT flag is set, then this means a regular user or anonymous
        // Submitted via the form so use the $userid value that was passed through
        $whosubmit = $userid;
    }else{
        // Otherwise, use the value that the technician selected in the form
        $whosubmit = $openedby;
    }
    // If the user has specified a Closed By or checked the "Close on Submit" checkbox, 
    // then set ticket status to Closed.
    if ($closeonsubmit) {
        // Checkbox is marked, set status to closed.
        $status = 3;
        // Now, who closed it?  If there is not a selection in closed by 
        // (UserID under 2 means anonymous or not set)
        // Then set to current userid
        if ($closedby < 2){
            $closedby = $userid;	
        }
        // If the checkbox is empty, we still need to check the closedby dropbox
    }elseif ($closedby > 1){
        $status = 3;
    } else {
        $status = 1;
    }
    
    // If there is not assigned to rep we will try and 
    // find a rep to assign the ticket to
    if(empty($assignedto)){  
       $assignedto = xarModAPIFunc('helpdesk', 'user', 'assignto', 
                                   array('cids' => $cids)
                                  );
    }
    
    $return_val = xarModAPIFunc('helpdesk','user','create',
                                array('userid'      => $userid,
                                      'name'        => $name,
                                      'whosubmit'   => $whosubmit,
                                      'phone'       => $phone,
                                      'email'       => $email,
                                      'subject'     => $subject,
                                      'domain'      => $domain,
                                      'sw_id'       => $sw_id,
                                      'swv_id'      => $swv_id,
                                      'source'      => $source,
                                      'priority'    => $priority,
                                      'status'      => $status,
                                      'type'        => $type,
                                      'openedby'    => $openedby,
                                      'assignedto'  => $assignedto,
                                      'closedby'    => $closedby,
                                      'issue'       => $issue,
                                      'notes'       => $notes,
                                      'hours'       => $hours,
                                      'minutes'     => $minutes,
                                      'closeonsubmit' => $closeonsubmit
                                      ));

    // Adds the Issue                                      
    $pid = 0; // parent id
    $itemid = $return_val; // id of ticket just created
    $result = xarModAPIFunc('comments', 'user', 'add', 
                            array('modid' => $modid,
                                  'objectid' => $itemid,
                                  'pid' => $pid,
                                  'title' => $subject,
                                  'comment' => $issue,
                                  'postanon' => 0,
                                  'author' =>  $userid                    
                                 )
                           );
                               
    if(!empty($notes)){
        $result = xarModAPIFunc('comments', 'user', 'add', 
                                array('modid' => $modid,
                                      'objectid' => $itemid,
                                      'pid' => $result,
                                      'title' => $subject,
                                      'comment' => $notes,
                                      'postanon' => 0,
                                      'author' =>  $userid
                                    )
                               );
    }                                                              
   
    // Cats
    xarModAPIFunc('categories', 'admin', 'linkcat', 
                  array('cids'        => $cids,
                        'iids'        => array($return_val),
                        'modid'       => $modid,
                        'itemtype'    => $itemtype,
                        'clean_first' => true)
                 );
     
    //xarResponseRedirect(xarModURL('helpdesk', 'user', 'main'));
                                      
    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if ($return_val === false) {
        return false;
    }else{
        $data['return_val'] = $return_val;
    }
    
    // Lets create hooks
    $item = array();
    $item['module'] = 'helpdesk';
    $item['itemtype'] = $itemtype;
    //$item['returnurl'] = xarModURL('helpdesk', 'user', 'main');
    $hooks = xarModCallHooks('item', 'create', $return_val, $item, 'helpdesk');
    if (empty($hooks)) {
        $data['hooks'] = '';
    } else {
        $data['hooks'] = $hooks;
    } 
                 
        
    $data['userid'] = $userid;
    $data['enabledimages']  = xarModGetVar('helpdesk', 'Enable Images');
    $data['menu']           = xarModFunc('helpdesk', 'user', 'menu');
    $data['mainmsg']        = xarML('Welcome to the Help Desk.  Please click a link to use the system.');
    $data['summaryfooter']  = xarModFunc('helpdesk', 'user', 'summaryfooter');
    $data['userisloggedin'] = xarUserIsLoggedIn();
   
    return xarTplModule('helpdesk', 'user', 'sendnewticket', $data);
}
?>
