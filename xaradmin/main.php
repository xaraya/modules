<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Add a standard screen upon entry to the module.
 *
 * @public
 * @author John Cox
 * @return bool true on success of redirect
 */
function html_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditHTML')) {
        return;
    }
    xarController::redirect(xarModURL('html', 'admin', 'set'));
    // Return the template variables defined in this function
    return true;
}
