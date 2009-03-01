<?php
/**
 * Display the user menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 */
/**
 * display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 *
 * @param $phase is the which part of the loop you are on
 *
 */
function xtasks_user_usermenu($args)
{
    extract($args);
    // Security check  - if the user has read access to the menu, show a
    // link to display the details of the item
    if (!xarSecurityCheck('ViewXTask', 0)) return;

    // First, lets find out where we are in our logic.  If the phase
    // variable is set, we will load the correct page in the loop.
    if(!xarVarFetch('phase','str', $phase, 'menu', XARVAR_NOT_REQUIRED)) {return;}
    
    $authid = xarSecGenAuthKey('xtasks');

    switch(strtolower($phase)) {
        case 'menu':
            $tplvars = array('icon' => 'modules/xtasks/xarimages/admin.gif');
            
            $data = xarTplModule('xtasks','user', 'usermenu_icon', $tplvars);
            break;

        case 'form':
            $tplvars = array();
            
            $data = xarTplModule('xtasks','user', 'usermenu_tasks', $tplvars);
            break;

        case 'settings':
            $tplvars = array('authid' => $authid);
            $tplvars['emailtaskupdates'] = xarModGetUserVar('xtasks','emailtaskupdates');
            $tplvars['show_owner'] = xarModGetUserVar('xtasks','show_owner');
            $tplvars['show_project'] = xarModGetUserVar('xtasks','show_project');
            $tplvars['show_client'] = xarModGetUserVar('xtasks','show_client');
            $tplvars['show_importance'] = xarModGetUserVar('xtasks','show_importance');
            $tplvars['show_priority'] = xarModGetUserVar('xtasks','show_priority');
            $tplvars['show_age'] = xarModGetUserVar('xtasks','show_age');
            $tplvars['show_hours'] = xarModGetUserVar('xtasks','show_hours');
            $tplvars['show_pctcomplete'] = xarModGetUserVar('xtasks','show_pctcomplete');
            $tplvars['show_planned_dates'] = xarModGetUserVar('xtasks','show_planned_dates');
            $tplvars['show_actual_dates'] = xarModGetUserVar('xtasks','show_actual_dates');
            $tplvars['verbose'] = xarModGetUserVar('xtasks','verbose');
            
            $data = xarTplModule('xtasks','user', 'usermenu_form', $tplvars);
            break;

        case 'update':
            // First we need to get the data back from the template in order to process it.
            // The example module is not setting any user vars at this time, but an example
            // might be the number of items to be displayed per page.
            if(!xarVarFetch('uid','int', $uid, 0, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('emailtaskupdates','checkbox', $emailtaskupdates, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('show_owner','checkbox', $agentid, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('show_project','checkbox', $contactid, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('show_client','checkbox', $show_client, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('show_importance','checkbox', $show_importance, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('show_priority','checkbox', $show_priority, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('show_age','checkbox', $show_age, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('show_hours','checkbox', $show_hours, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('show_pctcomplete','checkbox', $show_pctcomplete, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('show_planned_dates','checkbox', $show_planned_dates, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('show_actual_dates','checkbox', $show_actual_dates, NULL, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('verbose','checkbox', $verbose, NULL, XARVAR_NOT_REQUIRED)) {return;}

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;
//die("test: ".$agentid);
            xarModSetUserVar('xtasks','emailtaskupdates', $emailtaskupdates);
            xarModSetUserVar('xtasks','show_owner', $show_owner);
            xarModSetUserVar('xtasks','show_project', $show_project);
            xarModSetUserVar('xtasks','show_client', $show_client);
            xarModSetUserVar('xtasks','show_importance', $show_importance);
            xarModSetUserVar('xtasks','show_priority', $show_priority);
            xarModSetUserVar('xtasks','show_age', $show_age);
            xarModSetUserVar('xtasks','show_hours', $show_hours);
            xarModSetUserVar('xtasks','show_pctcomplete', $show_pctcomplete);
            xarModSetUserVar('xtasks','show_planned_dates', $show_planned_dates);
            xarModSetUserVar('xtasks','show_actual_dates', $show_actual_dates);
            xarModSetUserVar('xtasks','verbose', $verbose);
            
            // Redirect back to the account page.  We could also redirect back to our form page as
            // well by adding the phase variable to the array.
            xarResponseRedirect(xarModURL('roles', 'user', 'account',array('moduleload'=>'xtasks')));

            break;
    }
    
    // Finally, we need to send our variables to block layout for processing.  Since we are
    // using the data var for processing above, we need to do the same with the return.
    return $data;
}

?>
