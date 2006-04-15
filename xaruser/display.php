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
    if( !xarVarFetch('ticket_id', 'int:1:',  $ticket_id, null,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('tid',       'int:1:',  $ticket_id, null,  XARVAR_NOT_REQUIRED) ){ return false; }

    // Cheap way to pass info to security event
    //$_GET['itemype'] = TICKET_ITEMTYPE;
    //$_GET['itemid'] = $ticket_id;

    if( empty($ticket_id) )
    {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'ticket id', 'user', 'viewticket', 'helpdesk');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // Load the API
    if( !xarModAPILoad('helpdesk', 'user') ){ return false; }

    // Get the ticket Data:
    xarModAPILoad('security', 'user');
    $data = xarModAPIFunc('helpdesk', 'user', 'getticket',
        array(
            'tid' => $ticket_id,
            'security_level' => SECURITY_READ
        )
    );

    /*
        If we don't get a ticket back, then the user does not have privs
        to view this ticket
    */
    if( empty($data) ){ return false; }

    $data['history'] = xarModAPIFunc('helpdesk', 'user', 'getcomments',
        array(
            'itemid' => $ticket_id
        )
    );

    $data['statuses'] = xarModAPIFunc('helpdesk', 'user', 'gets',
        array(
            'itemtype' => STATUS_ITEMTYPE
        )
    );

    /*
        Call the hooks
    */
    $item = array();
    $item['module']    = 'helpdesk';
    $item['itemtype']  = TICKET_ITEMTYPE;
    $item['returnurl'] =  xarModURL('helpdesk', 'user', 'display', array('tid' => $ticket_id));
    $hooks = xarModCallHooks('item', 'display', $ticket_id, $item);
    if( empty($hooks) ){
        $data['hookoutput'] = array();
    }else {
        $data['hookoutput'] = $hooks;
    }

    $data['enabledimages'] = xarModGetVar('helpdesk', 'Enable Images');
    $data['menu']    = xarModFunc('helpdesk', 'user', 'menu');
    $data['summary'] = xarModFunc('helpdesk', 'user', 'summaryfooter');

    return xarTplModule('helpdesk', 'user', 'display', $data);
}
?>