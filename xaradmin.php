<?php

function vanilla_admin_main($args)
{
    // There is only one administrative function - just go straight there.
    return xarModFunc('vanilla', 'admin', 'modifyconfig', $args);
}


function vanilla_admin_modifyconfig($args)
{
    // Check privileges.
    // We will piggy-back the Authentication System module privileges.
    if (!xarSecurityCheck('AdminAuthsystem')) return;

    // Get the base path, if the user is submitting it.
    xarVarFetch('basepath', 'str:1:100', $new_basepath, NULL, XARVAR_NOT_REQUIRED);

    // Used for running login/logout tests.
    xarVarFetch('test', 'enum:login:logout', $test, NULL, XARVAR_NOT_REQUIRED);

    // If a new basepath has been submitted, then store it.
    if (!empty($new_basepath)) {
        xarModSetVar('vanilla', 'basepath', $new_basepath);
    }

    // There is only one parameter - the base directory.
    $basepath = xarModGetVar('vanilla', 'basepath');

    // Get the Vanilla settings
    $settings = xarModAPIfunc('vanilla', 'user', 'getsettings');

    // Roles
    $roles = xarModAPIfunc('vanilla', 'user', 'getroles');

    // Run the tests if required.
    if (!empty($test)) {
        if ($test == 'login') {
            xarModAPIfunc('vanilla', 'event', 'OnUserLogin');
        }
        if ($test == 'logout') {
            xarModAPIfunc('vanilla', 'event', 'OnUserLogout');
        }
    }

    return array(
        'basepath' => $basepath,
        'settings' => $settings,
        'roles' => $roles,
        'args' => $args,
    );
}

?>