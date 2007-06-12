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
 *
 * @return bool true on success of update
 */
function sitetools_admin_updateconfig()
{
    if (!xarVarFetch('adopath', 'str:4:254', $adopath, '')) return;
    if (!xarVarFetch('rsspath', 'str:4:254', $rsspath, '')) return;
    if (!xarVarFetch('templpath', 'str:4:254', $templpath,'')) return;
    if (!xarVarFetch('backuppath', 'str:4:254', $backuppath,'')) return;
    if (!xarVarFetch('defaultbktype', 'str:4', $defaultbktype,'')) return;
    if (!xarVarFetch('lineterm', 'str:2:4', $lineterm,'')) return;
    if (!xarVarFetch('usetimestamp', 'int:1:', $usetimestamp, true)) return;
    if (!xarVarFetch('usedbprefix', 'int:1:', $usedbprefix, false)) return;
    if (!xarVarFetch('colnumber', 'int:1:', $colnumber,3)) return;
    if (!xarVarFetch('confirm', 'str:4:254', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('ftpserver', 'str:4:254', $ftpserver, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ftpuser', 'str:2:254', $ftpuser, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ftpdir', 'str:1:254', $ftpdir, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ftppw', 'str:3:254', $ftppw, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('useftpbackup', 'checkbox', $useftpbackup, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('usesftpbackup', 'checkbox', $usesftpbackup, false, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    /* Update module variables.  Note that the default values are set in
     * xarVarFetch when recieving the incoming values, so no extra processing
     * is needed when setting the variables here.
     */
    $checkpath=array('adocachepath'   => $adopath,
                     'rsscachepath'   => $rsspath,
                     'templcachepath' => $templpath,
                     'backuppath'     => $backuppath);


    foreach ($checkpath as $varname=>$pathname) {
        $pathname= trim(ereg_replace('\/$', '', $pathname));
        if ($pathname == '') {
            $pathvar = substr($varname,0,3);
            switch ($pathvar) {
             case 'ado':
                 xarModSetVar('sitetools', 'adocachepath', sys::varpath()."/cache/adodb");
                 break;
             case 'tem':
                 xarModSetVar('sitetools', 'templcachepath', sys::varpath()."/cache/templates");
                 break;
             case 'rss':
                 xarModSetVar('sitetools', 'rsscachepath', sys::varpath()."/cache/templates");
                 break;
             case 'bac':
                 xarModSetVar('sitetools', 'backuppath', sys::varpath()."/uploads");
                 break;
            }

        } else {
            if (!file_exists($pathname) || !is_dir($pathname)) {
                $msg = xarML('Location [#(1)] either does not exist or is not a valid directory!', $pathname);
                xarErrorSet(XAR_USER_EXCEPTION, 'INVALID_DIRECTORY', new DefaultUserException($msg));
                return;
            } elseif (!is_writable($pathname)) {
                $msg = xarML('Location [#(1)] can not be written to - please check permissions and try again!', $pathname);
                xarErrorSet(XAR_USER_EXCEPTION, 'NOT_WRITABLE', new DefaultUserException($msg));
                return;
            } else {
                $match = array('/^\.\/var\//','/^var\//');
                    //replace any ./var or /var or var at the beginning of the path with the real var path
                   $pathname=preg_replace($match, sys::varpath().'/',$pathname);

                xarModSetVar('sitetools', $varname, $pathname);
            }
        }
    }


    /*    xarModSetVar('sitetools','lineterm', $lineterm);  */
    xarModSetVar('sitetools','timestamp', $usetimestamp);
    xarModSetVar('sitetools','usedbprefix', $usedbprefix);
    xarModSetVar('sitetools','colnumber',$colnumber);
    xarModSetVar('sitetools','defaultbktype',$defaultbktype);
    // FTP options
    xarModSetVar('sitetools','useftpbackup', $useftpbackup);
    xarModSetVar('sitetools','ftpserver', $ftpserver);
    xarModSetVar('sitetools','ftpuser', $ftpuser);
    xarModSetVar('sitetools','ftppw', $ftppw);
    xarModSetVar('sitetools','ftpdir', $ftpdir);
    // Secure FTP?
    xarModSetVar('sitetools','usesftpbackup', $usesftpbackup);

    if (xarModIsAvailable('scheduler')) {
        if (!xarVarFetch('interval', 'isset', $interval, array(), XARVAR_NOT_REQUIRED)) return;
        /* for each of the functions specified in the template */
        foreach ($interval as $func => $howoften) {
            /* see if we have a scheduler job running to execute this function */
            $job = xarModAPIFunc('scheduler','user','get',
                                 array('module' => 'sitetools',
                                       'type' => 'scheduler',
                                       'func' => $func));
            if (empty($job) || empty($job['interval'])) {
                if (!empty($howoften)) {
                    /* create a scheduler job */
                    xarModAPIFunc('scheduler','admin','create',
                                  array('module' => 'sitetools',
                                        'type' => 'scheduler',
                                        'func' => $func,
                                        'interval' => $howoften));
                }
            } elseif (empty($howoften)) {
                /* delete the scheduler job */
                xarModAPIFunc('scheduler','admin','delete',
                              array('module' => 'sitetools',
                                    'type' => 'scheduler',
                                    'func' => $func));
            } elseif ($howoften != $job['interval']) {
                /* update the scheduler job */
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

    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('sitetools', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
?>