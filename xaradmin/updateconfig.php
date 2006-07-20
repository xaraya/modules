<?php
/**
 * Update site configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage wizards
 * @link http://xaraya.com/index.php/release/3007.html
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
/**
 * Update site configuration
 *
 * @param string
 * @return void?
 */
function wizards_admin_updateconfig()
{
    if (!xarVarFetch('adminwizards','int',$adminwizards)) return;
    if (!xarVarFetch('userwizards','int',$userwizards)) return;

    // Security Check
    if(!xarSecurityCheck('AdminWizard')) return;

    $wizards = $adminwizards * 2 + $userwizards;
    xarModSetVar('wizards','status',$wizards);
    xarResponseRedirect(xarModURL('wizards', 'admin', 'modifyconfig'));

    return true;
}

?>
