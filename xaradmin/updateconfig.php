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
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author Tasks module development team
 */

function tasks_admin_updateconfig()
{
    if (!xarVarFetch('dateformat', 'str::', $dateformat, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showoptions', 'checkbox', $showoptions, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias','checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnfromadd', 'int', $returnfromadd, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnfromedit', 'int', $returnfromedit, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnfromsurface', 'int', $returnfromsurface, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnfrommigrate', 'int', $returnfrommigrate, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxdisplaydepth', 'int', $maxdisplaydepth, 5, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    xarModSetVar('tasks', 'dateformat', $dateformat);
    xarModSetVar('tasks', 'showoptions', $showoptions);
/*    xarModSetVar('tasks', 'returnfromadd', $returnfromadd);
    xarModSetVar('tasks', 'returnfromedit', $returnfromedit);
    xarModSetVar('tasks', 'returnfromsurface', $returnfromsurface);
    xarModSetVar('tasks', 'returnfrommigrate', $returnfrommigrate);
*/    xarModSetVar('tasks', 'maxdisplaydepth', $maxdisplaydepth);

    xarResponseRedirect(xarModURL('tasks', 'admin', 'modifyconfig'));

    return true;
}

?>