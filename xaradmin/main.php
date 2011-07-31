<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main admin GUI function, entry point
 *
 */

    function mime_admin_main()
    {
        xarController::redirect(xarModURL('mime', 'admin', 'view'));
        
        if(!xarSecurityCheck('ManageMime')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarController::redirect(xarModURL('mime', 'admin', 'view'));
        }
        // success
        return true;
    }
?>