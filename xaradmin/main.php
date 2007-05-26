<?php
/**
    Main admin module function.
*/
function sitesearch_admin_main($args)
{
    extract($args);

    // Security check
    if (!xarSecurityCheck('AdminSiteSearch')) return;
     
    if (xarModGetVar('adminpanels', 'overview') == 0)
    {
        $welcome = '';
        // Return the template variables defined in this function
        return array('welcome' => $welcome);
    } 
    else 
    {
        xarResponseRedirect(xarModURL('sitesearch', 'admin', 'modifyconfig'));
    }
   
    return true;
}
?>