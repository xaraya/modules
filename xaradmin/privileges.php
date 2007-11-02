<?php

/**
 * Handle privileges administration.
 * @todo To be completed - just a text instance at the moment.
 */

function mag_admin_privileges($args)
{
    extract($args);
    $return = array();

    // Parameters passed in from the privileges module.
    foreach(array('extpid', 'extname', 'extrealm', 'extmodule', 'extcomponent', 'extinstance', 'extlevel') as $ext_param) {
        xarVarFetch($ext_param, 'str', $$ext_param, '', XARVAR_DONT_SET);
        $return[$ext_param] = $$ext_param;
    }

    // Split the instance passed in, into parts.
    list($extmid, $extpremium) = split(':', $extinstance . '::');

    // Parameters for this module.
    xarVarFetch('mid', 'str:1', $mid, 'All', XARVAR_DONT_SET);
    xarVarFetch('premium', 'str:1', $premium, 'All', XARVAR_DONT_SET);

    // For submit buttons: update and stay, or apply and return.
    xarVarFetch('update', 'str', $update, '', XARVAR_DONT_SET);
    xarVarFetch('apply', 'str', $apply, '', XARVAR_DONT_SET);

    // If submitting the form, then use the submitted values,
    // otherwise take what was passed in from the privileges screen.
    if (empty($update) && empty($apply)) {
        $mid = $extmid;
        $premium = $extpremium;
    }

    // Recombine the instance parts.
    $instance = array($mid, $premium);

    // Return the data back to the privilages module.
    if (!empty($apply)) {
        // Create or update the privilege.
        $pid = xarReturnPrivilege($extpid, $extname, $extrealm, $extmodule, $extcomponent, $instance, $extlevel);

        // Throw back if not successful.
        if (empty($pid)) return;

        // Redirect back to the privilege module
        xarResponseRedirect(xarModURL('privileges', 'admin', 'modifyprivilege', array('pid' => $pid)));
        return true;
    }

    // Current instances for form.
    $return['mid'] = $mid;
    $return['premium'] = $premium;
    $return['instance'] = implode(':', $instance);

    // List values for form.

    // Magazines.
    $all_mags = xarModAPIfunc('mag', 'user', 'getmags');
    $return['mags'] = array();
    $return['mags'][] = array('id' => '', 'name' => 'All');
    foreach($all_mags as $mag) {
        $return['mags'][] = array('id' => $mag['mid'], 'name' => $mag['mid'] . ': ' . $mag['title']);
    }

    // Premium flags (the reason for all this code)
    $premium_flags = xarModAPIfunc('mag', 'list', 'premiumflags');
    $return['premium_flags'] = array();
    $return['premium_flags'][] = array('id' => '', 'name' => 'All');
    foreach($premium_flags as $key => $premium_flag) {
        $return['premium_flags'][] = array('id' => $key, 'name' => $key . ': ' . $premium_flag);
    }

    return $return;
}

?>