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
 * view items
 */
function logconfig_admin_view()
{
    xarLogMessage("Logconfig Internal test (info level)", XARLOG_LEVEL_INFO);
    xarLogMessage("Logconfig Internal test (error level)", XARLOG_LEVEL_ERROR);
    xarLogMessage("Logconfig Internal test (warning level)", XARLOG_LEVEL_WARNING);
    $data = xarModAPIFunc('logconfig','admin','menu');

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminLogConfig')) return;

    $data['itemsnum'] = xarModGetVar('logconfig','itemstypenumber');

    if (!xarModAPIFunc('logconfig','admin','islogon')
        && xarLogFallbackPossible())
    {
        $data['fallbackOn'] = true;
    } else {
        $data['fallbackOn'] = false;
    }

    $data['fallbackFile'] = xarLogFallbackFile();

    // Return the template variables defined in this function
    return $data;
}

?>