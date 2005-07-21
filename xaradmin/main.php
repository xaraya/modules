<?php

/*/
 * main admin function
 * called when user clicks the module link
 *
 * @returns template variables or redirects user to main admin page
/*/
function shopping_admin_main()
{
    // security check
    if (!xarSecurityCheck('EditShopping')) return;
    return array();
}
?>
