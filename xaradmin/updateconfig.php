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
   This is a standard function to update the configuration parameters of the
   module given the information passed back by the modification form
*/
function helpdesk_admin_updateconfig()
{
    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if( !xarSecConfirmAuthKey() ){ return false; }

    if( !Security::check(SECURITY_ADMIN, 'helpdesk') ){ return false; }


    if( !xarVarFetch('itemtype', 'int', $itemtype, TICKET_ITEMTYPE, XARVAR_NOT_REQUIRED) ){ return false; }

    if( $itemtype == TICKET_ITEMTYPE )
    {
        if( !xarVarFetch('rowsperpage',          'isset', $rowsperpage, '') ){ return false; }
        if( !xarVarFetch('pagecountlimit',       'isset', $pagecountlimit, '') ){ return false; }
        if( !xarVarFetch('anonymouscansubmit',   'isset', $anonymouscansubmit, '') ){ return false; }
        if( !xarVarFetch('usercansubmit',        'isset', $usercansubmit, '') ){ return false; }
        if( !xarVarFetch('usercancheckstatus',   'isset', $usercancheckstatus, '') ){ return false; }
        if( !xarVarFetch('techsseealltickets',   'isset', $techsseealltickets, '') ){ return false; }
        if( !xarVarFetch('enableimages',         'isset', $enableimages, '') ){ return false; }
        if( !xarVarFetch('tech_group',           'str', $tech_group, '') ){ return false; }
        if( !xarVarFetch('default_open_status',  'int', $default_open_status, null) ){ return false; }
        if( !xarVarFetch('open_statuses',        'array', $open_statuses, array()) ){ return false; }
        if( !xarVarFetch('default_resolved_status', 'int', $default_resolved_status, null) ){ return false; }
        if( !xarVarFetch('resolved_statuses',    'array', $resolved_statuses, array()) ){ return false; }
        if( !xarVarFetch('allowstatuschangeonsubmit',   'isset', $allowstatuschangeonsubmit, '') ){ return false; }
        if( !xarVarFetch('allowcloseonsubmit',          'isset', $allowcloseonsubmit, '') ){ return false; }
        if( !xarVarFetch('showopenbyinsummary',         'isset', $showopenbyinsummary, '') ){ return false; }
        if( !xarVarFetch('showassignedtoinsummary',     'isset', $showassignedtoinsummary, '') ){ return false; }
        if( !xarVarFetch('showclosedbyinsummary',       'isset', $showclosedbyinsummary, '') ){ return false; }
        if( !xarVarFetch('openedbydefaulttologgedin',   'isset', $openedbydefaulttologgedin, '') ){ return false; }
        if( !xarVarFetch('assignedtodefaulttologgedin', 'isset', $assignedtodefaulttologgedin, '') ){ return false; }
        if( !xarVarFetch('showlastmodifiedinsummary',   'isset', $showlastmodifiedinsummary, '') ){ return false; }
        if( !xarVarFetch('showdateenteredinsummary',    'isset', $showdateenteredinsummary, '') ){ return false; }
        if( !xarVarFetch('showpriorityinsummary',       'isset', $showpriorityinsummary, '') ){ return false; }
        if( !xarVarFetch('showstatusinsummary',         'isset', $showstatusinsummary, '') ){ return false; }
        if( !xarVarFetch('allowdomainname',             'isset', $allowdomainname, '') ){ return false; }
        if( !xarVarFetch('enablemystatshyperlink',      'isset', $enablemystatshyperlink, '') ){ return false; }

        // Update module variables.  Note that depending on the HTML structure used
        // to obtain the information from the user it is possible that the values
        // might be unset, so it is important to check them all and assign them
        // default values if required
        // These two lines may be needed when images are added to the Help Desk
        xarModSetVar('helpdesk', 'Default rows per page',   $rowsperpage);
        xarModSetVar('helpdesk', 'Page Count Limit',        $pagecountlimit);
        xarModSetVar('helpdesk', 'Anonymous can Submit',    $anonymouscansubmit);
        xarModSetVar('helpdesk', 'User can Submit',         $usercansubmit);
        xarModSetVar('helpdesk', 'User can check status',   $usercancheckstatus);
        xarModSetVar('helpdesk', 'Techs see all tickets',   $techsseealltickets);
        xarModSetVar('helpdesk', 'Enable Images',           $enableimages);
        xarModSetVar('helpdesk', 'tech_group',              $tech_group);
        xarModSetVar('helpdesk', 'default_open_status',     $default_open_status);
        xarModSetVar('helpdesk', 'open_statuses',           serialize($open_statuses));
        xarModSetVar('helpdesk', 'default_resolved_status', $default_resolved_status);
        xarModSetVar('helpdesk', 'resolved_statuses',       serialize($resolved_statuses));

        xarModSetVar('helpdesk', 'AllowStatusChangeOnSubmit', $allowstatuschangeonsubmit);
        xarModSetVar('helpdesk', 'AllowCloseOnSubmit',      $allowcloseonsubmit);
        xarModSetVar('helpdesk', 'ShowOpenedByInSummary',   $showopenbyinsummary);
        xarModSetVar('helpdesk', 'ShowAssignedToInSummary', $showassignedtoinsummary);
        xarModSetVar('helpdesk', 'ShowClosedByInSummary',   $showclosedbyinsummary);
        xarModSetVar('helpdesk', 'OpenedByDefaultToLoggedIn',   $openedbydefaulttologgedin);
        xarModSetVar('helpdesk', 'AssignedToDefaultToLoggedIn', $assignedtodefaulttologgedin);
        xarModSetVar('helpdesk', 'ShowLastModifiedInSummary',   $showlastmodifiedinsummary);
        xarModSetVar('helpdesk', 'ShowDateEnteredInSummary',    $showdateenteredinsummary);
        xarModSetVar('helpdesk', 'ShowPriorityInSummary',       $showpriorityinsummary);
        xarModSetVar('helpdesk', 'ShowStatusInSummary',         $showstatusinsummary);
        xarModSetVar('helpdesk', 'AllowDomainName',             $allowdomainname);
        xarModSetVar('helpdesk', 'EnableMyStatsHyperLink',      $enablemystatshyperlink);
    }

    xarModCallHooks('module','updateconfig','helpdesk',
        array(
            'module'   => 'helpdesk',
            'itemtype' => $itemtype
        )
    );

    // this second hooks call should sync cats for the main mod with the ones for the reps
    // which is what we really need
    xarModCallHooks('module','updateconfig','helpdesk',
        array(
            'module'   => 'helpdesk',
            'itemtype' => REPRESENTATIVE_ITEMTYPE
        )
    );

    xarResponseRedirect(xarModURL('helpdesk', 'admin', 'modifyconfig'));

    //Return
    return false;
}
?>
