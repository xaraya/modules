<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage HTML Module
 * @link http://xaraya.com/index.php/release/779.html
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
    if(!xarSecurityCheck('EditHTML')) return;
    xarResponse::Redirect(xarModURL('html', 'admin', 'set'));
    // Return the template variables defined in this function
    return true;
}

?>
