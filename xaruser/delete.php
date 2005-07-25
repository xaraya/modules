<?php

function helpdesk_user_delete($args)
{    
    xarVarFetch('tid',        'int:1:',  $tid,        null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('confirm',    'isset',   $confirm,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',   'int',     $itemtype,   1,     XARVAR_NOT_REQUIRED);
    
    if( !xarModAPILoad('helpdesk', 'user') ) { return false; }
         
    $isticketowner = xarModAPIFunc('helpdesk', 'user', 'ticketowner', 
                                   array('ticket_id' => $tid, 
                                         'userid'    => xarUserGetVar('uid')));

    if( !$isticketowner && !xarSecurityCheck('deletehelpdesk', 0) ) {
        $msg = xarML('Illegal Access - You are not allowed to be here!');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    if( !empty($confirm) )
    {
        $item = array();
        $item['objectid'] = $tid;
        $item['itemtype'] = $itemtype;
        $item['module'] = 'helpdesk';
        xarModCallHooks('item', 'delete', $tid, $item);
        
        $result = xarModAPIFunc('helpdesk', 'user', 'delete', array('tid' => $tid));
    
        xarResponseRedirect(xarModURL('helpdesk', 'user', 'view'));
    }
    
    $data = array();
    $data['tid'] = $tid;
    $data['UserLoggedIn']   = xarUserIsLoggedIn();
    $data['enforceauthkey'] = xarModGetVar('helpdesk', 'EnforceAuthKey');
    $data['username'] = xarUserGetVar('uname');
    $data['userid']   = xarUserGetVar('uid');
    
    return $data;
}
?>