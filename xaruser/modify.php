<?php
/**
  Modify a ticket item
  
  @author Brian McGilligan
  @return Template data
*/
function helpdesk_user_modify($args)
{
    extract($args);
    
    xarVarFetch('tid',        'int:1:',  $tid,        null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('confirm',    'isset',   $confirm,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',   'int',     $itemtype,   1,     XARVAR_NOT_REQUIRED);
    
    if (!xarModAPILoad('helpdesk', 'user')) { return false; }
    if (!xarModAPILoad('security', 'user')) { return false; }
         
    // If we have confirmation do the update
    if( !empty($confirm) )
    {
        $enforceauthkey = xarModGetVar('helpdesk', 'EnforceAuthKey');
        if ( $enforceauthkey && !xarSecConfirmAuthKey() ){ return false; }
        
        /*
            Security check to prevent un authorized users from modifying it
        */
        $has_security = xarModAPIFunc('security', 'user', 'check',
            array(
                'modid'     => xarModGetIDFromName('helpdesk'),
                'itemtype'  => $itemtype,
                'itemid'    => $tid,
                'level'     => SECURITY_WRITE
            )
        );
        if( !$has_security ){ return false; }
        
        $item = array();
        $item['module'] = 'helpdesk';
        $item['itemtype'] = $itemtype;        
        $hooks = xarModCallHooks('item', 'update', $tid, $item);
        if (empty($hooks)) {
            $data['hookoutput'] = array();
        }else {
            $data['hookoutput'] = $hooks;
        }
        
        $updateresult = xarModAPIFunc('helpdesk', 'user', 'update');
        
        xarResponseRedirect(xarModURL('helpdesk', 'user', 'view',
            array(
                'tid'       => $tid,
                'selection' => 'MYALL' // MYALL includes assigned tickets now
            )
        ));
        
        return true;                           
    }    
    
    /*
        Get the ticket Data, if we can not get it then we must not have privs for it.
    */
    $data['ticketdata']   = xarModAPIFunc('helpdesk','user','getticket',
        array(
            'tid'            => $tid,
            'security_level' => SECURITY_WRITE
        )
    );
    if( empty($data['ticketdata']) )
    {
        $msg = xarML("You do not have the proper security clearance to view this ticket!");
        xarErrorSet(XAR_USER_EXCEPTION, 'NO_PRIVILEGES', $msg);
        return false;    
    }
    
    /*
        These funcs should be rethought once we get the rest working
    */
    $data['priority'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
        array(
            'itemtype' => 2
        )
    );
    $data['status'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
        array(
            'itemtype' => 3
        )
    );
    $data['sources'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
        array(
            'itemtype' => 4
        )
    );
    $data['reps'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
        array(
            'itemtype' => 10
        )
    );
        
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
    
    $data['tid']            = $tid;    
    $data['menu']           = xarModFunc('helpdesk', 'user', 'menu');
    $data['EditAccess']     = xarSecurityCheck('edithelpdesk', 0);
    $data['UserLoggedIn']   = xarUserIsLoggedIn();
    $data['enforceauthkey'] = xarModGetVar('helpdesk', 'EnforceAuthKey');
    $data['enabledimages']  = xarModGetVar('helpdesk', 'Enable Images');    
    $data['summary']        = xarModFunc('helpdesk', 'user', 'summaryfooter');    
        
    return xarTplModule('helpdesk', 'user', 'modify', $data);    
}
?>
