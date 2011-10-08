<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
        xarController::redirect(xarModURL('publications', 'admin', 'view'));
    // success
    return true;

}

?>
