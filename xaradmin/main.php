<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */
/**
 * Add a standard screen upon entry to the module.
 * @return bool true on success of redirect
 */
function hitcount_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminHitcount')) return;
    xarResponseRedirect(xarModURL('hitcount', 'admin', 'modifyconfig'));
    // success
    return true;
}

?>