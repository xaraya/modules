<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * the main administration function
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function workflow_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

        xarResponse::Redirect(xarModURL('workflow', 'admin', 'processes'));
    // success
    return true;
}

?>
