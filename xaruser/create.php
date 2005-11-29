<?php
/**
    create an item
    
    @author Brian McGilligan
    @return
*/
function helpdesk_user_create($args)
{
    $allowanonadd = xarModGetVar('helpdesk', 'Anonymous Can Submit'); 
    
    if (empty($allowanonadd)){
        if (!xarSecurityCheck('readhelpdesk')) return false;
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
    xarVarFetch('name',     'str:1:',   $name,      '',  XARVAR_NOT_REQUIRED);
    xarVarFetch('userid',   'int:1:',   $userid,    0,  XARVAR_NOT_REQUIRED);
    xarVarFetch('phone',    'str:1:',   $phone,     '',  XARVAR_NOT_REQUIRED);
    xarVarFetch('email',    'email:1:', $email,     '',  XARVAR_NOT_REQUIRED);
    xarVarFetch('domain',   'str:1:',   $domain,    '',  XARVAR_NOT_REQUIRED);
    xarVarFetch('subject',  'str:1:',   $subject,   '',  XARVAR_NOT_REQUIRED);
    xarVarFetch('nontech',  'str:1:',   $nontech,   '',  XARVAR_NOT_REQUIRED);
    xarVarFetch('source',   'int:1:',   $source,    0,  XARVAR_NOT_REQUIRED);
    xarVarFetch('status',   'int:1:',   $status,    2,     XARVAR_NOT_REQUIRED); // default = 2 or Open
    xarVarFetch('priority', 'int:1:',   $priority,  0,  XARVAR_NOT_REQUIRED);
    xarVarFetch('openedby', 'int:1:',   $openedby,  0,  XARVAR_NOT_REQUIRED);
    xarVarFetch('assignedto','int:1:',  $assignedto,0,  XARVAR_NOT_REQUIRED);
    xarVarFetch('closedby', 'int:1:',   $closedby,  0,  XARVAR_NOT_REQUIRED);
    xarVarFetch('issue',    'str:1:',   $issue,     '',  XARVAR_NOT_REQUIRED);
    xarVarFetch('notes',    'str:1:',   $notes,     '',  XARVAR_NOT_REQUIRED);
    xarVarFetch('cids',     'array',    $cids,      array(),    XARVAR_NOT_REQUIRED);
    xarVarFetch('closeonsubmit', 'int', $closeonsubmit,  0,  XARVAR_NOT_REQUIRED);
    
    if ($nontech || $openedby == 0){
        // If the NONTECHSUBMIT flag is set, then this means a regular user or anonymous
        // Submitted via the form so use the $userid value that was passed through
        $whosubmit = $userid;
    }else{
        // Otherwise, use the value that the technician selected in the form
        $whosubmit = $openedby;
    }
    
    // If it is closed by someone, the ticket must be closed
    if(!empty($closedby))
        $status = 3;
    
    
    // If there is not assigned to rep we will try and 
    // find a rep to assign the ticket to
    if(empty($assignedto)){  
       $assignedto = xarModAPIFunc('helpdesk', 'user', 'assignto', 
                                   array('cids' => $cids)
                                  );
    }
    
    // If closed by field is empty, then set closed by to 0
    if (empty($closedby)) {
        $closedby = 0;
    }

    $return_val = xarModAPIFunc('helpdesk','user','create',
        array(
            'userid'      => $userid,
            'name'        => $name,
            'whosubmit'   => $whosubmit,
            'phone'       => $phone,
            'email'       => $email,
            'subject'     => $subject,
            'domain'      => $domain,
            'source'      => $source,
            'priority'    => $priority,
            'status'      => $status,
            'openedby'    => $openedby,
            'assignedto'  => $assignedto,
            'closedby'    => $closedby,
            'issue'       => $issue,
            'notes'       => $notes
        )
    );

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if ($return_val === false) {
        return false;
    }else{
        $data['return_val'] = $return_val;
    }

    /**
        Send an e-mail to user with details
        @author MichelV.
        $mail needs to be set
    */
    $usernew = 'usernew';
    $mail =xarModFunc('helpdesk','user','sendmail',
        array(
            'userid'      => $userid,
            'name'        => $name,
            'whosubmit'   => $whosubmit,
            'phone'       => $phone,
            'email'       => $email,
            'subject'     => $subject,
            'domain'      => $domain,
            'source'      => $source,
            'priority'    => $priority,
            'status'      => $status,
            'openedby'    => $openedby,
            'assignedto'  => $assignedto,
            'closedby'    => $closedby,
            'issue'       => $issue,
            'notes'       => $notes,
            'tid'         => $return_val,
            'mailaction'  => $usernew 
        )
    );
    
    // Check if the email has been sent.
    if ($mail === false) {
        $msg = xarML('Email to user was not sent!');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
        new SystemException($msg));
    }
    
    /**
    * Send an e-mail to assignedto
    * @author MichelV.
    * $mail needs to be set
    */
    $assignednew = 'assignednew';
    $assignedmail =xarModFunc('helpdesk','user','sendmail',
        array(
            'userid'      => $userid,
            'name'        => $name,
            'whosubmit'   => $whosubmit,
            'phone'       => $phone,
            'email'       => $email,
            'subject'     => $subject,
            'domain'      => $domain,
            'source'      => $source,
            'priority'    => $priority,
            'status'      => $status,
            'openedby'    => $openedby,
            'assignedto'  => $assignedto,
            'closedby'    => $closedby,
            'issue'       => $issue,
            'notes'       => $notes,
            'tid'         => $return_val,
            'mailaction'  => $assignednew
        )
    );
    
    // Check if the email has been sent.
    if ($assignedmail === false) {
        $msg = xarML('Email to assigned-to was not sent!');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
        new SystemException($msg));
    }

    // Adds the Issue                                      
    $pid = 0; // parent id
    $itemid = $return_val; // id of ticket just created
    $itemtype = 1;
    $result = xarModAPIFunc('comments', 'user', 'add', 
                            array('modid'    => $modid,
                                  'objectid' => $itemid,
                  'itemtype' => $itemtype,
                                  'pid'      => $pid,
                                  'title'    => $subject,
                                  'comment'  => $issue,
                                  'postanon' => 0,
                                  'author'   => $userid                    
                                 )
                           );
                               
    if(!empty($notes)){
        $result = xarModAPIFunc('comments', 'user', 'add', 
                                array('modid'    => $modid,
                                      'objectid' => $itemid,
                      'itemtype' => $itemtype,
                                      'pid'      => $result,
                                      'title'    => $subject,
                                      'comment'  => $notes,
                                      'postanon' => 0,
                                      'author'   =>  $userid
                                    )
                               );
    }                                                              
   
    
    // Lets create hooks
    $item = array();
    $item['module'] = 'helpdesk';
    $item['itemtype'] = $itemtype;
    $item['returnurl'] = xarModURL('helpdesk', 'user', 'display', array('tid' => $return_val));
    $hooks = xarModCallHooks('item', 'create', $return_val, $item, 'helpdesk');
    if (empty($hooks)) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    } 
                 
    /*
        Get template ready to display message to user.
    */   
    $data['userid'] = $userid;
    $data['enabledimages']  = xarModGetVar('helpdesk', 'Enable Images');
    $data['menu']           = xarModFunc('helpdesk', 'user', 'menu');
    $data['summaryfooter']  = xarModFunc('helpdesk', 'user', 'summaryfooter');
    $data['userisloggedin'] = xarUserIsLoggedIn();
   
    return xarTplModule('helpdesk', 'user', 'sendnewticket', $data);
}
?>