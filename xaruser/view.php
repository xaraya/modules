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
   View Tickets

   @param $selection - The Key of what is being viewed (optional)
   @param $startnum - id of item to start the page with (optional)
   @param $sortorder - The order in which we do the sort (optional)

   @return module output
*/
function helpdesk_user_view($args)
{
    // Get arguments
    extract($args);

    if( !xarVarFetch('selection',    'str:1:',  $selection,  null,   XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('sortorder',    'str:1:',  $sortorder,  null,   XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('order',        'str:1:',  $order,      null,   XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('startnum',     'str:1:',  $startnum,    null,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('statusfilter', 'str:1:',  $statusfilter,null,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('company',      'str:1:',  $company,     null,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('catid',        'str',     $catid,       '',  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('search_fields','array', $search_fields, null,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('keywords',     'str',     $keywords,    null,  XARVAR_NOT_REQUIRED) ){ return false; }

    // Load the needed APIs
    if( !xarModAPILoad('helpdesk', 'user') ){ return false; }
    if( !xarModAPILoad('security', 'user') ){ return false; }

    if( !Security::check(SECURITY_READ, 'helpdesk', TICKET_ITEMTYPE) ){ return false; }

    $check_session_vars = array(
        'selection' => 'MYPERSONAL',
        'sortorder' => 'TICKET_ID',
        'order' => 'ASC',
        'startnum' => 1,
        'statusfilter' => '',
        'company' => '',
        'catid' => ''
    );

    foreach( $check_session_vars as $var => $default_value )
    {
        // Hack to deal with category ids
        if( $var != 'catid' )
        {
            $old_var = xarSessionGetVar("Modules.helpdesk.view.$var");
            if( empty($$var) )
            {
                if( !empty($old_var) ){ $$var = $old_var; }
                else{ $$var = $default_value; }
            }
            // If any filter has changed we need to start over
            if( $old_var != $$var && $var != 'startnum' && $startnum != 1 )
            {
                xarSessionSetVar("Modules.helpdesk.view.startnum", $startnum=1);
            }
            // If a filter is -1 we no longer need to use it
            if( $$var == -1 ){ $$var = ''; }
            xarSessionSetVar("Modules.helpdesk.view.$var", $$var);
        }
    }

//    if( !xarSecurityCheck('edithelpdesk', 0) )
//    {
//        $selection = 'MYPERSONAL';
//    }

    $data = array();
    // Lets get the ticket now for the view
    $data['tickets']  = xarModAPIFunc('helpdesk', 'user', 'gettickets',
        array(
            'userid'       => xarUserGetVar('uid'),
            'catid'        => $catid,
            'selection'    => $selection,
            'sortorder'    => $sortorder,
            'order'        => $order,
            'startnum'     => $startnum,
            'statusfilter' => $statusfilter,
            'company'      => $company,
            'search_fields'=> $search_fields,
            'keywords'     => $keywords
        )
    );

    /*
        Counts the number of tickets in the system
    */
    $totaltickets  = xarModAPIFunc('helpdesk', 'user', 'count_tickets',
        array(
            'userid'       => xarUserGetVar('uid'),
            'catid'        => $catid,
            'selection'    => $selection,
            'sortorder'    => $sortorder,
            'order'        => $order,
            'startnum'     => $startnum,
            'statusfilter' => $statusfilter,
            'company'      => $company,
            'search_fields'=> $search_fields,
            'keywords'     => $keywords
        )
    );

    /*
        Setup args for pager so we don't lose our place
    */
    $args = array(
        'selection'    => $selection,
        'sortorder'    => $sortorder,
        'order'        => $order,
        'statusfilter' => $statusfilter,
        'company'      => $company,
        'search_fields'=> $search_fields,
        'keywords'     => $keywords,
        'startnum'     => '%%'
    );
    $url_template   = xarModURL('helpdesk', 'user', 'view', $args);
    $items_per_page = xarModGetVar('helpdesk', 'Default rows per page');
    $data['pager']  = xarTplGetPager($startnum, $totaltickets, $url_template, $items_per_page);

    $data['selections'] = array(
        'MYALL'         => xarML('My Tickets'),
        'MYPERSONAL'    => xarML('My Tickets'),
        'ALL'           => xarML('All Tickets'),
        'MYASSIGNEDALL' => xarML('My Assigned Tickets'),
        'UNASSIGNED'    => xarML('Unassigned Tickets')
    );

    // Sending state vars back into the form
    $data['catid']        = $catid;
    $data['selection']    = $selection;
    $data['sortorder']    = $sortorder;
    $data['order']        = $order;
    $data['statusfilter'] = $statusfilter;
    $data['status']       = xarModAPIFunc('helpdesk', 'user', 'gets',
        array('itemtype' => STATUS_ITEMTYPE)
    );
    $data['companies']    = xarModAPIFunc('helpdesk', 'user', 'get_companies');
    $data['company']      = $company;

    // Get Column View preferences
    $data['showassignedtoinsummary']    = xarModGetVar('helpdesk', 'ShowAssignedToInSummary');
    $data['showclosedbyinsummary']      = xarModGetVar('helpdesk', 'ShowClosedByInSummary');
    $data['showopenbyinsummary']        = xarModGetVar('helpdesk', 'ShowOpenedByInSummary');
    $data['showlastmodifiedinsummary']  = xarModGetVar('helpdesk', 'ShowLastModifiedInSummary');
    $data['showdateenteredinsummary']   = xarModGetVar('helpdesk', 'ShowDateEnteredInSummary');
    $data['showstatusinsummary']        = xarModGetVar('helpdesk', 'ShowStatusInSummary');
    $data['showpriorityinsummary']      = xarModGetVar('helpdesk', 'ShowPriorityInSummary');

    return xarTplModule('helpdesk', 'user', 'view', $data);
}
?>
