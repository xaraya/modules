<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
function headlines_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;
    if(!xarSecurityCheck('AdminHeadlines')) return;
    if (!xarVarFetch('itemsperpage', 'str:1:', $itemsperpage, '20', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('magpie', 'checkbox', $magpie, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('importpubtype', 'id', $importpubtype, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uniqueid', 'str:1:', $uniqueid, '', XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('headlines', 'itemsperpage', $itemsperpage);
    xarModSetVar('headlines', 'SupportShortURLs', $shorturls);
    xarModSetVar('headlines', 'magpie', $magpie);
    xarModSetVar('headlines', 'importpubtype', $importpubtype);
    xarModSetVar('headlines', 'uniqueid', $uniqueid);
    xarModCallHooks('module','updateconfig','headlines', array('module' => 'headlines'));
    xarResponseRedirect(xarModURL('headlines', 'admin', 'modifyconfig'));
    return true;
}
?>
