<?php
/**
 * Ephemerids Module
 *
 * @package modules
 * @subpackage ephemerids module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * update configuration
 */
function ephemerids_admin_updateconfig()
{
    if (!xarVarFetch('itemsperpage','int:1:',$itemsperpage, 10)) return;
    if (!xarSecConfirmAuthKey()) return;
    if(!xarSecurityCheck('AdminEphemerids')) return;
    xarModVars::set('ephemerids', 'itemsperpage', $itemsperpage);
    xarController::redirect(xarModURL('ephemerids', 'admin', 'modifyconfig'));
    return true;
}

?>