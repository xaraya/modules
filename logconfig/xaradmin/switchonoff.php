<?php
/**
 * File: $Id:
 *
 * GUI Function to switch the logging system on/off
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage log
 * @author Flavio Botelho
 */

/**
 * GUI Function to switch the logging system on/off
 */
function logconfig_admin_switchonoff ()
{
    if (!xarSecurityCheck('AdminLogConfig')) return;

    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('logconfig', 'admin', 'switchonoff')) return;

    $data = xarModAPIFunc('logconfig','admin','menu');

    $data['islogon'] = xarModAPIFunc('logconfig','admin','islogon');
    
    return $data;
}

?>