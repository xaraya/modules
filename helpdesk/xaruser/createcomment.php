<?php
/**
  create a comment on a ticket
  @author Brian McGilligan
  
*/
function helpdesk_user_createcomment($args)
{
    extract($args);

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
    
    //if (!xarModAPILoad('helpdesk', 'user')) return false;

    // Get parameters from whatever input we need.
    
    xarVarFetch('uid',      'int:1:',   $uid,       null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('tid',      'int:1:',   $tid,       null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('pid',      'int:1:',   $pid,       null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('subject',  'str:1:',   $subject,   null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('comment',  'str:1:',   $comment,   null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('notes',    'str:1:',   $notes,     null,  XARVAR_NOT_REQUIRED);
        
    $result = xarModAPIFunc('comments', 'user', 'add', 
                            array('modid'    => $modid,
                                  'objectid' => $tid,
                                  'itemtype' => $itemtype,
                                  'pid'      => $pid,
                                  'title'    => $subject,
                                  'comment'  => $comment,
                                  'postanon' => 0,
                                  'author'   => $uid                    
                                 )
                           );  
    
    xarResponseRedirect(xarModURL('helpdesk', 'user', 'display', array('tid' => $tid)));
                                      
    return;
}
?>