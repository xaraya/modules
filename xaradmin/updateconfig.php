<?php
/**
 *
 * Modify configuration settings for the commerce module
 *
 * @package modules
 * @copyright (C) 2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage commerce
 * @link  link to information for the subpackage
 * @author author name Marc Lutolf <mfl@netspan.ch>
 */

//---------------------------------------------------------
// Update Configuration Wrapper
//---------------------------------------------------------
function commerce_admin_updateconfig()
{
	if (!xarSecConfirmAuthKey()) return;

    if (!xarVarFetch('tab', 'str', $tab, 'modifyconfig_general', XARVAR_NOT_REQUIRED)) return;
    $configarea = explode('_',$tab);
    if (!$args = xarModAPIFunc($configarea[0], 'admin', 'updateconfig_' . $configarea[1]))
    	throw new Exception('Configuration update failed');
    xarResponseRedirect(xarModURL('commerce', 'admin', 'modifyconfig', array('tab' => $tab, 'args' => $args)));
    return true;
}
?>