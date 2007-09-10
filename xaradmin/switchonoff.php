<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * GUI Function to switch the logging system on/off
 * @author Flavio Botelho
 */
function logconfig_admin_switchonoff ()
{
    if (!xarSecurityCheck('AdminLogConfig')) return;

    if (!xarSecConfirmAuthKey()) return;


    $isLogOn = xarModAPIFunc('logconfig','admin','islogon');

    if ($isLogOn) {
        if (!xarModAPIFunc('logconfig', 'admin', 'turnoff')) return;
    } else {
        if (!xarModAPIFunc('logconfig', 'admin', 'turnon')) return;
    }

    $data = xarModAPIFunc('logconfig','admin','menu');
    $data['previousState'] = $isLogOn;
    $data['currentState'] = xarModAPIFunc('logconfig','admin','islogon');

    return $data;
}

?>