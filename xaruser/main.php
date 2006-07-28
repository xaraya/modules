<?php
/**
 * Admin interface for the products module
 *
 * @package Commerce
 * @subpackage Products Module
 * @author Marc Lutolf (mfl@netspan.ch)
 *  -----------------------------------------------------------------------------------------
 *  based on:
 *  (c) 2003 XT-Commerce
 *  (c) 2003  nextcommerce (product_reviews_info.php,v 1.12 2003/08/17); www.nextcommerce.org
 *  (c) 2002-2003 osCommerce(product_reviews_info.php,v 1.47 2003/02/13); www.oscommerce.com
 *  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
*/

/**
 * the main user function
 */
function products_user_main()
{
    if(!xarSecurityCheck('EditProducts')) return;

    xarSessionSetVar('commerce_statusmsg', xarML('Commerce Main User Menu',
                    'commerce'));

    if(!xarVarFetch('branch', 'str', $branch,   "start", XARVAR_NOT_REQUIRED)) {return;}

    if (xarModGetVar('modules', 'disableoverview') == 0) {
        return array();
    }
    else {
        switch(strtolower($branch)) {
            case 'start':
                xarResponseRedirect(xarModURL('products', 'user', 'start'));
                break;
        }
   }
}
?>
