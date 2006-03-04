<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * the main administration function
 * This function redirects to the view categories function
 * @return bool true on success
 */
function categories_admin_main()
{
    // Security check
    if(!xarSecurityCheck('ViewCategories')) return;
    // Redirect
    xarResponseRedirect(xarModURL('categories', 'admin', 'viewcats'));
    return true;
}

?>