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

    // Update module variables.  Note that depending on the HTML structure used
    // to obtain the information from the user it is possible that the values
    // might be unset, so it is important to check them all and assign them
    // default values if required
    // These two lines may be needed when images are added to the Help Desk
    xarModSetVar('helpdesk', 'EnforceAuthKey', xarVarCleanFromInput('enforceauthkey'));
    xarModSetVar('helpdesk', 'Default rows per page', xarVarCleanFromInput('rowsperpage'));
    xarModSetVar('helpdesk', 'Page Count Limit',xarVarCleanFromInput('pagecountlimit'));
    xarModSetVar('helpdesk', 'Anonymous can Submit', xarVarCleanFromInput('anonymouscansubmit'));
    xarModSetVar('helpdesk', 'User can Submit', xarVarCleanFromInput('usercansubmit'));
    xarModSetVar('helpdesk', 'User can check status', xarVarCleanFromInput('usercancheckstatus'));
    xarModSetVar('helpdesk', 'Techs see all tickets', xarVarCleanFromInput('techsseealltickets'));
    xarModSetVar('helpdesk', 'Enable Images',xarVarCleanFromInput('enableimages'));
    xarModSetVar('helpdesk', 'AllowCloseOnSubmit',xarVarCleanFromInput('allowcloseonsubmit'));
    xarModSetVar('helpdesk', 'ShowOpenedByInSummary',xarVarCleanFromInput('showopenbyinsummary'));
    xarModSetVar('helpdesk', 'ShowAssignedToInSummary',xarVarCleanFromInput('showassignedtoinsummary'));
    xarModSetVar('helpdesk', 'ShowClosedByInSummary',xarVarCleanFromInput('showclosedbyinsummary'));
    xarModSetVar('helpdesk', 'OpenedByDefaultToLoggedIn',xarVarCleanFromInput('openedbydefaulttologgedin'));
    xarModSetVar('helpdesk', 'AssignedToDefaultToLoggedIn',xarVarCleanFromInput('assignedtodefaulttologgedin'));
    xarModSetVar('helpdesk',	'ShowLastModifiedInSummary',xarVarCleanFromInput('showlastmodifiedinsummary'));
    xarModSetVar('helpdesk',	'ShowDateEnteredInSummary',xarVarCleanFromInput('showdateenteredinsummary'));
    xarModSetVar('helpdesk', 'ShowPriorityInSummary',xarVarCleanFromInput('showpriorityinsummary'));
    xarModSetVar('helpdesk', 'ShowStatusInSummary',xarVarCleanFromInput('showstatusinsummary'));
    xarModSetVar('helpdesk', 'AllowSoftwareChoice',xarVarCleanFromInput('allowsoftwarechoice'));
    xarModSetVar('helpdesk', 'AllowVersionChoice',xarVarCleanFromInput('allowversionchoice'));
    xarModSetVar('helpdesk', 'AllowDomainName',xarVarCleanFromInput('allowdomainname'));
    xarModSetVar('helpdesk', 'EnableMyStatsHyperLink',xarVarCleanFromInput('enablemystatshyperlink'));
    
    if (!xarVarFetch('itemtype', 'int', $itemtype, 1, XARVAR_NOT_REQUIRED)) return;
            
    xarModCallHooks('module','updateconfig','helpdesk',
                    array('module'   => 'helpdesk',
                          'itemtype' => $itemtype)
                   ); 
    
    xarResponseRedirect(xarModURL('helpdesk', 'admin', 'modifyconfig'));

    //Return
    return true;
}
?>
