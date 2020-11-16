<?php
/**
 * Site Tools Update Configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function sitetools_admin_updateconfig()
{
    if (!xarVar::fetch('adopath', 'str:4:128', $adopath, '')) {
        return;
    }
    if (!xarVar::fetch('rsspath', 'str:4:128', $rsspath, '')) {
        return;
    }
    if (!xarVar::fetch('templpath', 'str:4:128', $templpath, '')) {
        return;
    }
    if (!xarVar::fetch('backuppath', 'str:4:128', $backuppath, '')) {
        return;
    }
    if (!xarVar::fetch('defaultbktype', 'str:4', $defaultbktype, '')) {
        return;
    }
    if (!xarVar::fetch('lineterm', 'str:2:4', $lineterm, '')) {
        return;
    }
    if (!xarVar::fetch('usetimestamp', 'int:1:', $usetimestamp, true)) {
        return;
    }
    if (!xarVar::fetch('usedbprefix', 'int:1:', $usedbprefix, false)) {
        return;
    }
    if (!xarVar::fetch('colnumber', 'int:1:', $colnumber, 3)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'str:4:128', $confirm, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarSec::confirmAuthKey()) {
        return;
    }
    /* Update module variables.  Note that the default values are set in
     * xarVar::fetch when recieving the incoming values, so no extra processing
     * is needed when setting the variables here.
     */
    xarModVars::set('sitetools', 'adocachepath', $adopath);
    xarModVars::set('sitetools', 'rsscachepath', $rsspath);
    xarModVars::set('sitetools', 'templcachepath', $templpath);
    xarModVars::set('sitetools', 'backuppath', $backuppath);
    /*    xarModVars::set('sitetools','lineterm', $lineterm);  */
    xarModVars::set('sitetools', 'timestamp', $usetimestamp);
    xarModVars::set('sitetools', 'usedbprefix', $usedbprefix);
    xarModVars::set('sitetools', 'colnumber', $colnumber);
    xarModVars::set('sitetools', 'defaultbktype', $defaultbktype);

    if (xarMod::isAvailable('scheduler')) {
        if (!xarVar::fetch('interval', 'isset', $interval, array(), xarVar::NOT_REQUIRED)) {
            return;
        }
        /* for each of the functions specified in the template */
        foreach ($interval as $func => $howoften) {
            /* see if we have a scheduler job running to execute this function */
            $job = xarMod::apiFunc(
                'scheduler',
                'user',
                'get',
                array('module' => 'sitetools',
                                       'type' => 'scheduler',
                                       'func' => $func)
            );
            if (empty($job) || empty($job['interval'])) {
                if (!empty($howoften)) {
                    /* create a scheduler job */
                    xarMod::apiFunc(
                        'scheduler',
                        'admin',
                        'create',
                        array('module' => 'sitetools',
                                        'type' => 'scheduler',
                                        'func' => $func,
                                        'interval' => $howoften)
                    );
                }
            } elseif (empty($howoften)) {
                /* delete the scheduler job */
                xarMod::apiFunc(
                    'scheduler',
                    'admin',
                    'delete',
                    array('module' => 'sitetools',
                                    'type' => 'scheduler',
                                    'func' => $func)
                );
            } elseif ($howoften != $job['interval']) {
                /* update the scheduler job */
                xarMod::apiFunc(
                    'scheduler',
                    'admin',
                    'update',
                    array('module' => 'sitetools',
                                    'type' => 'scheduler',
                                    'func' => $func,
                                    'interval' => $howoften)
                );
            }
        }
    }

    xarModHooks::call(
        'module',
        'updateconfig',
        'sitetools',
        array('module' => 'sitetools')
    );

    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponse::Redirect(xarController::URL('sitetools', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
