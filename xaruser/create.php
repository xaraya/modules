<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
    create an item

    @author Brian McGilligan
    @return
*/
function helpdesk_user_create($args)
{
    $allowanonadd = xarModGetVar('helpdesk', 'Anonymous Can Submit');

    if (empty($allowanonadd)){
        if( !xarSecurityCheck('readhelpdesk') ){ return false; }
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
    xarModAPILoad('helpdesk');
    $itemtype = TICKET_ITEMTYPE;

    // Get parameters from whatever input we need.
    if( !xarVarFetch('name',     'str:1:',   $name,      '', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('userid',   'int:1:',   $userid,    0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('phone',    'str:1:',   $phone,     '', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('email',    'email:1:', $email,     '', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('domain',   'str:1:',   $domain,    '', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('subject',  'str:1:',   $subject,   '', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('nontech',  'str:1:',   $nontech,   '', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('source',   'int:1:',   $source,    0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('status',   'int:1:',   $status,    0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( empty($status) ){ $status = xarModGetVar('helpdesk', 'default_open_status'); }
    if( !xarVarFetch('priority', 'int:1:',   $priority,  0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('openedby', 'int:1:',   $openedby,  0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('assignedto','int:1:',  $assignedto,0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('closedby', 'int:1:',   $closedby,  0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('issue',    'str:1:',   $issue,     '', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('notes',    'str:1:',   $notes,     '', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('cids',     'array',    $cids, array(), XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('closeonsubmit','int', $closeonsubmit,0,XARVAR_NOT_REQUIRED) ){ return false; }

    if ($nontech || $openedby == 0){
        // If the NONTECHSUBMIT flag is set, then this means a regular user or anonymous
        // Submitted via the form so use the $userid value that was passed through
        $whosubmit = $userid;
    }else{
        // Otherwise, use the value that the technician selected in the form
        $whosubmit = $openedby;
    }

    if( empty($name) ){ $name = xarUserGetVar('name', $whosubmit); }
    if( empty($email) ){ $email = xarUserGetVar('email', $whosubmit); }

    // If it is closed by someone, the ticket must be closed
    if( !empty($closedby) ){ $status = xarModGetVar('helpdesk', 'default_resolved_status'); }

    // If there is not assigned to rep we will try and
    // find a rep to assign the ticket to
    if( empty($assignedto) )
    {
        $assignedto = xarModAPIFunc('helpdesk', 'user', 'assignto',
            array('cids' => $cids)
        );
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

    /*
        Lets create hooks
        I have the hooks before the send mail because if there is a problem sending mail the hooks
        were not executing which was very very bad. Hooks must execute if the ticket was created
        otherwise users will not be able to see their ticket because of how the security module works
        - Brian McGilligan
    */
    $item = array();
    $item['module'] = 'helpdesk';
    $item['itemtype'] = $itemtype;
    $hooks = xarModCallHooks('item', 'create', $return_val, $item, 'helpdesk');

    /**
        Send an e-mail to user with details
        @author MichelV.
        $mail needs to be set
    */
    $mail = xarModFunc('helpdesk','user','sendmail',
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
            'mailaction'  => 'usernew'
        )
    );

    // Check if the email has been sent.
    if( $mail === false )
    {
        $msg = xarML('Email to user was not sent!');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
        new SystemException($msg));
    }

    /**
    * Send an e-mail to assignedto
    * @author MichelV.
    * $mail needs to be set
    */
    $assignedmail = xarModFunc('helpdesk','user','sendmail',
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
            'mailaction'  => 'assignednew'
        )
    );

    // Check if the email has been sent.
    if( $assignedmail === false )
    {
        $msg = xarML('Email to assigned-to was not sent!');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
        new SystemException($msg));
    }

    // Adds the Issue
    $itemid = $return_val; // id of ticket just created
    $itemtype = TICKET_ITEMTYPE;
    $result = xarModAPIFunc('comments', 'user', 'add',
        array(
            'modid'    => $modid,
            'objectid' => $itemid,
            'itemtype' => $itemtype,
            'title'    => $subject,
            'comment'  => $issue,
            'postanon' => 0,
            'author'   => $userid
        )
    );

    if( !empty($notes) )
    {
        $result = xarModAPIFunc('comments', 'user', 'add',
            array(
                'modid'    => $modid,
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
