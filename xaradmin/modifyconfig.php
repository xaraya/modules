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
// Modify Configuration Wrapper
//---------------------------------------------------------
function commerce_admin_modifyconfig($args)
{
    if (!xarVarFetch('update', 'isset', $update, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('args', 'array', $data['args'], array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tab', 'str', $data['tab'], 'commerce_general', XARVAR_NOT_REQUIRED)) return;
    $hooks = xarModCallHooks('module', 'getconfig', 'commerce');
	if (!empty($hooks) && isset($hooks['tabs'])) {
		foreach ($hooks['tabs'] as $key => $row) {
			$configarea[$key]  = $row['configarea'];
			$configtitle[$key] = $row['configtitle'];
			$configcontent[$key] = $row['configcontent'];
		}
		array_multisort($configtitle, SORT_ASC, $hooks['tabs']);
	} else {
		$hooks['tabs'] = array();
	}
	$data['hooks'] = $hooks;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
