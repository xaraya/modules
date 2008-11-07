<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @param string trigger
 */
function scheduler_admin_updateconfig()
{
    if (!xarSecurityCheck('AdminScheduler')) return;
    if (!xarSecConfirmAuthKey()) return;

      // TODO: move this to modify
//    if (!xarVarFetch('reset','isset',$reset,0,XARVAR_NOT_REQUIRED)) return;

    xarModCallHooks('module','updateconfig','scheduler',
                    array('module' => 'scheduler'));

    xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));

    return true;
}
?>
