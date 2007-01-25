<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * the main administration function
 *
 * It currently redirects to the admin-view function
 * @return bool true on success
 */
function articles_admin_main()
{

// Security Check
    if (!xarSecurityCheck('EditArticles')) return;
       $welcome = '';

        // Return the template variables defined in this function
        //return array('welcome' => $welcome);
        xarResponseRedirect(xarModURL('articles', 'admin', 'view'));
    // success
    return true;

}

?>
