<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * the main administration function
 *
 * It currently redirects to the admin-view function
 * @return bool true on success
 */
function publications_admin_main()
{

// Security Check
    if (!xarSecurityCheck('EditPublications')) return;
       $welcome = '';

        // Return the template variables defined in this function
        //return array('welcome' => $welcome);
        xarResponseRedirect(xarModURL('publications', 'admin', 'view'));
    // success
    return true;

}

?>
