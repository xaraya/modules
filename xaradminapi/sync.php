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
 * Synchronize the configuration cache file in PHP with the DB configuration
 */
function logconfig_adminapi_sync()
{
    if (!xarModAPIFunc('logconfig','admin','islogon')) {
        //do nothing
        return true;
    }
    //else

    if (!xarModAPIFunc('logconfig','admin','saveconfig')) return;

    return true;
}

?>