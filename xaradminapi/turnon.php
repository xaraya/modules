<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * Turn Logging on
 * @author Flavio Botelho <nuncanada@xaraya.com>
 */
function logconfig_adminapi_turnon()
{
    if (!xarModAPIFunc('logconfig','admin','saveconfig')) return false;

    return true;
}

?>