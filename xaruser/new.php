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
  Creates a new ticket

  @author Brian McGilligan
  @returns A new ticket form
*/
function helpdesk_user_new()
{
    if( !Security::check(SECURITY_WRITE, 'helpdesk', TICKET_ITEMTYPE) ){ return false; }

    if( !xarVarFetch('create', 'str:1:20', $create_ticket, null, XARVAR_NOT_REQUIRED) ){ return false; }

    // IE6 was using a cached copy of this page.  So IE6 was not getting the new AuthKey
    // This will force IE to get the new page each time a ticket submission is attempted
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

    // Ticket was submitted lets try and create it
    // If errors are found we will just fall thru.
    if( !is_null($create_ticket) )
    {
        if( !xarSecConfirmAuthKey() )
        {
            xarResponseRedirect(xarModURL('helpdesk', 'user', 'main'));
            return false;
        }

        // Get some info about the mod and a ticket type id
        $modid = xarModGetIDFromName('helpdesk');
        $itemtype = TICKET_ITEMTYPE;

        // Get parameters from whatever input we need.
        if( !xarVarFetch('name',     'str:1:',   $name,      '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('userid',   'int:1:',   $userid,    0,  XARVAR_NOT_REQUIRED) ){ return false; }
        if( empty($userid) ){ $userid = xarUserGetVar('uid'); }
        if( !xarVarFetch('phone',    'str:1:',   $phone,     '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('email',    'str:1:',   $email,     '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('domain',   'str:1:',   $domain,    '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('subject',  'str:1:',   $subject,   '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('nontech',  'str:1:',   $nontech,   '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('source',   'int:1:',   $source,    0,  XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('status',   'int:1:',   $status,    0,  XARVAR_NOT_REQUIRED) ){ return false; }
        if( empty($status) ){ $status = xarModGetVar('helpdesk', 'default_open_status'); }
        if( !xarVarFetch('priority', 'int:1:',   $priority,  0,  XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('openedby', 'int:1:',   $openedby,  $userid,  XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('assignedto','int:1:',  $assignedto,0,  XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('closedby', 'int:1:',   $closedby,  0,  XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('issue',    'html:basic',$issue,     '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('notes',    'html:basic',$notes,     '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('new_cids', 'array',    $cids, array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('closeonsubmit','int', $closeonsubmit,0,XARVAR_NOT_REQUIRED) ){ return false; }

        if( empty($name) ){ $name = xarUserGetVar('name', $openedby); }
        $tmp_email = xarUserGetVar('email', $openedby);
        if( xarCurrentErrorID() == 'NOT_LOGGED_IN' )
        {
            // caused by anonymous users just use the email address already entered
            xarErrorHandled();
        }
        else if( $tmp_email != false )
        {
            // user was logged in we could get the an email address
            $email = $tmp_email;
        }

        //$email = ''; // simulate no email address

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

        // Check Required Variables. Make sure they are filled out and valid.
        $invalid = false;
        if( empty($name) )
        {
            $invalid = true;
        }
        if( empty($subject) )
        {
            $invalid = true;
        }
        if( !xarVarValidate('email', $email, true) )
        {
            $invalid = true;
        }
        if( empty($issue) )
        {
            $invalid = true;
        }

        if( $invalid === false )
        {
            // Ticket is valid and ok to create
            $return_val = xarModAPIFunc('helpdesk','user','create',
                array(
                    'name'        => $name,
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
                )
            );

            // The return value of the function is checked here, and if the function
            // suceeded then an appropriate message is posted.  Note that if the
            // function did not succeed then the API function should have already
            // posted a failure message so no action is required
            if ($return_val === false) {
                return false;
            }else{
                $data['tid']        = $return_val;
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
                    'phone'       => $phone,
                    'email'       => $email, // Needed for anon submitted tickets
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
                    'mailaction'  => 'new'
                )
            );
            // Check if the email has been sent.
            if( $mail === false )
            {
                $msg = xarML('Email to user was not sent!');
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
                    'author'   => $openedby
                    // A tech could can create a ticket for a user. The issue could
                    // entered by the tech but it should have been described by the user
                    // So I want this to be owned by the openedby user (User with the problem).
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
            $data['module'] = 'helpdesk';
            $data['itemtype'] = TICKET_ITEMTYPE;
            $data['userid'] = $userid;
            $data['enabledimages']  = xarModGetVar('helpdesk', 'Enable Images');
            $data['userisloggedin'] = xarUserIsLoggedIn();

            return xarTplModule('helpdesk', 'user', 'sendnewticket', $data);
        }

        // Ticket was found to be invalid so lets send user input back
        $ticket = array(
            'userid'      => $openedby,
            'username'    =>  xarUserGetVar('uname', $openedby),
            'name'        => $name,
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
        );

    }

    // Some of these values get used more than once in this procedure.
    // Make the call to get their value here to prevent multiple function calls
    // and/or db queries
    $data['allowcloseonsubmit']    = xarModGetVar('helpdesk', 'AllowCloseOnSubmit');
    $data['readaccess']            = xarSecurityCheck('readhelpdesk', 0);
    $data['editaccess']            = xarSecurityCheck('edithelpdesk', 0);
    $data['adminaccess']           = xarSecurityCheck('adminhelpdesk', 0);
    $data['userisloggedin']        = xarUserIsLoggedIn();

    if (!xarVarFetch('itemtype', 'int', $itemtype, TICKET_ITEMTYPE, XARVAR_NOT_REQUIRED)) return;

    if( !isset($ticket) )
    {
        $ticket = array(
            'userid'      => xarUserGetVar('uid'),
            'username'    => xarUserGetVar('uname'),
            'name'        => xarUserGetVar('name'),
            'phone'       => '',
            'email'       => $data['userisloggedin'] ? xarUserGetVar('email'):'',
            'subject'     => '',
            'domain'      => '',
            'source'      => '',
            'priority'    => '',
            'status'      => '',
            'openedby'    => '',
            'assignedto'  => '',
            'closedby'    => '',
            'issue'       => '',
            'notes'       => '',
        );
    }
    $data['ticket'] = $ticket;

    /*
    * These funcs should be rethought once we get the rest working
    */
    $data['priority'] = xarModAPIFunc('helpdesk', 'user', 'gets',
        array('itemtype' => PRIORITY_ITEMTYPE)
    );

    $data['sources'] = xarModAPIFunc('helpdesk', 'user', 'gets',
        array('itemtype' => SOURCE_ITEMTYPE)
    );

    $data['status'] = xarModAPIFunc('helpdesk', 'user', 'gets',
        array('itemtype' => STATUS_ITEMTYPE)
    );

    if( $data['editaccess'] )
    {
        $data['reps'] = xarModAPIFunc('helpdesk', 'user', 'gets',
            array('itemtype' => REPRESENTATIVE_ITEMTYPE)
        );
        $data['users'] = xarModAPIFunc('roles', 'user', 'getall');
    }

    /*
        Get the companies the current user has access to
    */
    $data['groups'] = xarModAPIFunc('helpdesk', 'user', 'get_companies',
        array(
            'parent' => 'Companies',
        )
    );

    $item = array();
    $item['module']   = 'helpdesk';
    $item['itemtype'] = $itemtype;
    $item['multiple'] = false;
    $item['returnurl'] = xarModURL('helpdesk', 'user', 'main');
    $data['hooks'] = xarModCallHooks('item', 'new', $itemtype, $item, 'helpdesk');

    $data['module'] = 'helpdesk';
    $data['itemtype'] = TICKET_ITEMTYPE;

    return xarTplModule('helpdesk', 'user', 'new', $data);
}
?>

