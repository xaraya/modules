<?php
/**
 * @package Xaraya modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Formantibot
 * @copyright (C) 2008 2skies.com
 * @link http://xarigami.com/project/formantibot
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function formantibot_admin_updateconfig()
{

    if (!xarVarFetch('registered',   'checkbox', $registered, false, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
   
    xarModSetVar('formantibot', 'registered', $registered);

    xarModCallHooks('module','updateconfig','formantibot',
                   array('module' => 'formantibot'));

    xarResponseRedirect(xarModURL('formantibot', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
?>