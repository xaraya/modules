<?php
/**
 * Sitemapper Module
 *
 * @package modules
 * @subpackage sitemapper module
 * @category Third Party Xaraya Module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Main admin GUI function, entry point
 *
 */

    function sitemapper_admin_main()
    {
        if (!xarSecurityCheck('EditSitemapper')) {
            return;
        }

        $refererinfo = xarController::$request->getInfo(xarServer::getVar('HTTP_REFERER'));
        $info = xarController::$request->getInfo();
        $samemodule = $info[0] == $refererinfo[0];

        if (((bool)xarModVars::get('modules', 'disableoverview') == false) || $samemodule) {
            return xarTplModule('sitemapper', 'admin', 'overview');
        } else {
            xarController::redirect(xarModURL('sitemapper', 'admin', 'view'));
            return true;
        }
    }
