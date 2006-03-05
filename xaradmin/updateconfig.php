<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
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
    xarModSetVar('ephemerids', 'itemsperpage', $itemsperpage);
    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'modifyconfig'));
    return true;
}

?>