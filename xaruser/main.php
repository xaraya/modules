<?php
/**
 * Default user function
 *
 * @subpackage Vendors module
 */
/**
 * the main user function
*/
function vendors_user_main()
{
    if(!xarSecurityCheck('ReadVendors')) return;

       xarResponseRedirect(xarModURL('vendors',
                                     'user',
                                     'view'));
}

?>