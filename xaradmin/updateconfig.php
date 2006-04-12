<?php
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
    if (xarModGetVar('helpdesk', 'EnforceAuthKey')){
        if (!xarSecConfirmAuthKey()) {
            return false;
        }
    }

    if (!xarVarFetch('itemtype', 'int', $itemtype, 1, XARVAR_NOT_REQUIRED)) return;

    if($itemtype == 1)
    {
    xarVarFetch('enforceauthkey',       'isset', $enforceauthkey, '');
    xarVarFetch('rowsperpage',          'isset', $rowsperpage, '');
    xarVarFetch('pagecountlimit',       'isset', $pagecountlimit, '');
    xarVarFetch('anonymouscansubmit',   'isset', $anonymouscansubmit, '');
    xarVarFetch('usercansubmit',        'isset', $usercansubmit, '');
    xarVarFetch('usercancheckstatus',   'isset', $usercancheckstatus, '');
    xarVarFetch('techsseealltickets',   'isset', $techsseealltickets, '');
    xarVarFetch('enableimages',         'isset', $enableimages, '');
    xarVarFetch('tech_group',           'isset', $tech_group, '');
    xarVarFetch('allowstatuschangeonsubmit',   'isset', $allowstatuschangeonsubmit, '');
    xarVarFetch('allowcloseonsubmit',          'isset', $allowcloseonsubmit, '');
    xarVarFetch('showopenbyinsummary',         'isset', $showopenbyinsummary, '');
    xarVarFetch('showassignedtoinsummary',     'isset', $showassignedtoinsummary, '');
    xarVarFetch('showclosedbyinsummary',       'isset', $showclosedbyinsummary, '');
    xarVarFetch('openedbydefaulttologgedin',   'isset', $openedbydefaulttologgedin, '');
    xarVarFetch('assignedtodefaulttologgedin', 'isset', $assignedtodefaulttologgedin, '');
    xarVarFetch('showlastmodifiedinsummary',   'isset', $showlastmodifiedinsummary, '');
    xarVarFetch('showdateenteredinsummary',    'isset', $showdateenteredinsummary, '');
    xarVarFetch('showpriorityinsummary',       'isset', $showpriorityinsummary, '');
    xarVarFetch('showstatusinsummary',         'isset', $showstatusinsummary, '');
    xarVarFetch('allowdomainname',             'isset', $allowdomainname, '');
    xarVarFetch('enablemystatshyperlink',      'isset', $enablemystatshyperlink, '');

    // Update module variables.  Note that depending on the HTML structure used
    // to obtain the information from the user it is possible that the values
    // might be unset, so it is important to check them all and assign them
    // default values if required
    // These two lines may be needed when images are added to the Help Desk
    xarModSetVar('helpdesk', 'EnforceAuthKey',          $enforceauthkey);
    xarModSetVar('helpdesk', 'Default rows per page',   $rowsperpage);
    xarModSetVar('helpdesk', 'Page Count Limit',        $pagecountlimit);
    xarModSetVar('helpdesk', 'Anonymous can Submit',    $anonymouscansubmit);
    xarModSetVar('helpdesk', 'User can Submit',         $usercansubmit);
    xarModSetVar('helpdesk', 'User can check status',   $usercancheckstatus);
    xarModSetVar('helpdesk', 'Techs see all tickets',   $techsseealltickets);
    xarModSetVar('helpdesk', 'Enable Images',           $enableimages);
    xarModSetVar('helpdesk', 'tech_group',              $tech_group);

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
                    array('module'   => 'helpdesk',
                          'itemtype' => $itemtype)
            );

    // this second hooks call should sync cats for the main mod with the ones for the reps
    // which is what we really need
    xarModCallHooks('module','updateconfig','helpdesk',
                    array('module'   => 'helpdesk',
                          'itemtype' => 10)
            );

    xarResponseRedirect(xarModURL('helpdesk', 'admin', 'modifyconfig'));

    //Return
    return true;
}
?>
