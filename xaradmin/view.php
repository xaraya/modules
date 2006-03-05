<?php
/**
 * Tasks module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Tasks Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Tasks Module Development Team
 */
/**
 * @author Chad Kraeft
 * View tasklist
 *
 */
function tasks_admin_view()
{
    xarResponseRedirect(xarModURL('tasks','user','view'));
    return true;
}

?>