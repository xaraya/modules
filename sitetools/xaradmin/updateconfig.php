<?php
/*
 * File: $Id:
 *
 * Update configuration variables
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteTools module
 * @author jojodee <jojodee@xaraya.com>
*/

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function sitetools_admin_updateconfig()
{

   if (!xarVarFetch('adopath', 'str:4:128', $adopath, '')) return;
    if (!xarVarFetch('rsspath', 'str:4:128', $rsspath, '')) return;
    if (!xarVarFetch('templpath', 'str:4:128', $templpath,'')) return;
    if (!xarVarFetch('backuppath', 'str:4:128', $backuppath,'')) return;
    if (!xarVarFetch('defaultbktype', 'str:4', $defaultbktype,'')) return;
    if (!xarVarFetch('lineterm', 'str:2:4', $lineterm,'')) return;
    if (!xarVarFetch('usetimestamp', 'int:1:', $usetimestamp, true)) return;
    if (!xarVarFetch('usedbprefix', 'int:1:', $usedbprefix, false)) return;
    if (!xarVarFetch('colnumber', 'int:1:', $colnumber,3)) return;
    if (!xarVarFetch('confirm', 'str:4:128', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.  Note that the default values are set in 
    // xarVarFetch when recieving the incoming values, so no extra processing
    // is needed when setting the variables here.
    xarModSetVar('sitetools','adocachepath',$adopath);
    xarModSetVar('sitetools','rsscachepath', $rsspath);
    xarModSetVar('sitetools','templcachepath', $templpath);
    xarModSetVar('sitetools','backuppath', $backuppath);
//    xarModSetVar('sitetools','lineterm', $lineterm);
    xarModSetVar('sitetools','timestamp', $usetimestamp);
    xarModSetVar('sitetools','usedbprefix', $usedbprefix);    
    xarModSetVar('sitetools','colnumber',$colnumber);
    xarModSetVar('sitetools','defaultbktype',$defaultbktype);

    if (xarModIsAvailable('scheduler')) {
        if (!xarVarFetch('interval', 'isset', $interval, array(), XARVAR_NOT_REQUIRED)) return;
        // for each of the functions specified in the template
        foreach ($interval as $func => $howoften) {
            // see if we have a scheduler job running to execute this function
            $job = xarModAPIFunc('scheduler','user','get',
                                 array('module' => 'sitetools',
                                       'type' => 'scheduler',
                                       'func' => $func));
            if (empty($job) || empty($job['interval'])) {
                if (!empty($howoften)) {
                    // create a scheduler job
                    xarModAPIFunc('scheduler','admin','create',
                                  array('module' => 'sitetools',
                                        'type' => 'scheduler',
                                        'func' => $func,
                                        'interval' => $howoften));
                }
            } elseif (empty($howoften)) {
                // delete the scheduler job
                xarModAPIFunc('scheduler','admin','delete',
                              array('module' => 'sitetools',
                                    'type' => 'scheduler',
                                    'func' => $func));
            } elseif ($howoften != $job['interval']) {
                // update the scheduler job
                xarModAPIFunc('scheduler','admin','update',
                              array('module' => 'sitetools',
                                    'type' => 'scheduler',
                                    'func' => $func,
                                    'interval' => $howoften));
            }
        }
    }

    xarModCallHooks('module','updateconfig','sitetools',
                   array('module' => 'sitetools'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('sitetools', 'admin', 'modifyconfig'));

    // Return
    return true;
}
?>
