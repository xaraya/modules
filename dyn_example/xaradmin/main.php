<?php

/**
 * the main administration function
 */
function dyn_example_admin_main()
{
    if (!xarSecurityCheck('EditDynExample')) return;

    $data = xarModAPIFunc('dyn_example','admin','menu');

    // Return the template variables defined in this function
    return $data;
}

?>
