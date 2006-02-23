<?php
/**
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @author John Cox
 * @access public
 * @return array
 * @throws no exceptions
 */
function pmember_admin_modifyconfig()
{
    if (!xarSecurityCheck('AdminPMember')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch (strtolower($phase)) {
        case 'modify':
        default:
            $roles = new xarRoles();
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');
            // create the dropdown of groups for the template display
            // get the array of all groups
            // remove duplicate entries from the list of groups
            $groups = array();
            $names = array();
            foreach($roles->getgroups() as $temp) {
                $nam = $temp['name'];
                if (!in_array($nam, $names)) {
                    array_push($names, $nam);
                    array_push($groups, $temp);
                }
            }
            // Get the current defult group for comparison
            $data['defaultgroup']   = xarModGetVar('pmember', 'defaultgroup');
            $data['groups']         = $groups;
            // Need to set the time and how often the subscription is for
            $data['typeoffee']      = xarModGetVar('pmember', 'typeoffee');
            $data['price']          = xarModGetVar('pmember', 'price');
            $data['period']         = xarModGetVar('pmember', 'period');
            $data['time']           = xarModGetVar('pmember', 'time');
            // Need to check to see if we want to send a thank you note.
            $data['sendmail']       = xarModGetVar('pmember', 'sendmail');
            // If so, let's get that here
            $data['message']        = xarModGetVar('pmember', 'message');
            // Finally, a little configurable option to explain the benefits of subscribing
            $data['benefits']       = xarModGetVar('pmember', 'benefits');
            // blee blee that's all folks.
            return $data;

        break;
        case 'update':
            if (!xarVarFetch('sendmail', 'checkbox', $sendmail, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('defaultgroup', 'str:1', $defaultgroup, 'Users', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('typeoffee', 'str', $typeoffee, 'subscription', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('period', 'str', $period, 'monthly', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('price', 'str', $price, '10.00', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('time', 'str', $time, 'M', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('message', 'str', $message, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('benefits', 'str', $benefits, '', XARVAR_NOT_REQUIRED)) return;

            xarModSetVar('pmember', 'sendmail', $sendmail);
            xarModSetVar('pmember', 'defaultgroup', $defaultgroup);
            xarModSetVar('pmember', 'typeoffee', $typeoffee);
            xarModSetVar('pmember', 'period', $period);
            xarModSetVar('pmember', 'time', $time);
            xarModSetVar('pmember', 'price', $price);
            xarModSetVar('pmember', 'message', $message);
            xarModSetVar('pmember', 'benefits', $benefits);

        return xarResponseRedirect(xarModURL('pmember', 'admin', 'modifyconfig'));
        break;
    }
    return true;
}
?>