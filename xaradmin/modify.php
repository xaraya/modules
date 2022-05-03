<?php
/**
 * @package modules
 * @subpackage logconfig
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2002-2022 The Digital Development Foundation
 * @copyright (C) 20229 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Logconfig module development team
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Configure a logger
 */
function logconfig_admin_modify($args)
{
    if (!xarVar::fetch('logger',   'str',      $logger,          '',    xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('confirm',  'checkbox', $data['confirm'], false, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('exit',     'checkbox', $data['exit'],    false, xarVar::NOT_REQUIRED)) return;

    if (empty($logger)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'logconfig');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    sys::import('modules.dynamicdata.class.objects.base');
    $objectname = 'logconfig_' . $logger;
	$data['object'] = DataObjectMaster::getObject(['name' => $objectname]);
	$data['object'] = xarMod::apiFunc('logconfig', 'admin', 'charge_loggerobject', array('logger' => $data['object']));

    $data['tplmodule'] = 'logconfig';

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('logconfig','admin','modify', $data);        
        } else {
            // Good data: save the data
            xarMod::apiFunc('logconfig', 'admin', 'discharge_loggerobject', array('logger' => $data['object']));

            if ($data['exit']) {
				// Jump to the next page
				xarController::redirect(xarModURL('logconfig','admin','view'));
				return true;
            }
        }
    }
    return $data;
}

?>