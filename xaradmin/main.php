<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * the main administration function
 */
function scheduler_admin_main()
{
    if (!xarSecurityCheck('AdminScheduler')) return;

        xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));

    return true;
}

?>
