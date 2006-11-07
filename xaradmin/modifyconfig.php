<?php
/**
 * Site Tools Modify Configuration
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
 * This is a standard function to modify the configuration parameters of the
 * module
 * @return array
 */
function sitetools_admin_modifyconfig()
{
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration
     */
    $data = xarModAPIFunc('sitetools', 'admin', 'menu');
    /* Security check - important to do this as early as possible */
    if (!xarSecurityCheck('AdminSiteTools')) return;
    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    /* Specify some labels and values for display */
    $data['adopath']       = xarModGetVar('sitetools','adocachepath');
    $data['rsspath']       = xarModGetVar('sitetools','rsscachepath');
    $data['templpath']     = xarModGetVar('sitetools','templcachepath');
    $data['backuppath']    = xarModGetVar('sitetools','backuppath');
    $data['usetimestamp']  = xarModGetVar('sitetools','timestamp');
    $data['lineterm']      = xarModGetVar('sitetools','lineterm');
    $data['colnumber']     = xarModGetVar('sitetools','colnumber');
    $data['defaultbktype'] = xarModGetVar('sitetools','defaultbktype');
    $data['defaultbktype'] = xarModGetVar('sitetools','defaultbktype');
    $data['usedbprefix']   = xarModGetVar('sitetools','usedbprefix');

    $data['ftpextension']  = extension_loaded('ftp');
    $data['useftpchecked'] = xarModGetVar('sitetools','useftpbackup') ? true : false;
    $data['ftpserver']     = xarModGetVar('sitetools','ftpserver');
    $data['ftpuser']       = xarModGetVar('sitetools','ftpuser');
    $data['ftppw']         = xarModGetVar('sitetools','ftppw');
    $data['ftpdir']        = xarModGetVar('sitetools','ftpdir');

    $data['defadopath']   = xarCoreGetVarDirPath()."/cache/adodb";
    $data['defrsspath']   = xarCoreGetVarDirPath()."/cache/rss";
    $data['deftemplpath'] = xarCoreGetVarDirPath()."/cache/templates";

    /* scheduler functions available in sitetools at the moment */
    $schedulerapi = array('optimize','backup');
    /* Define for each job type */
    $data['schedule']['optimize']=xarML('Run Optimize Job');
    $data['schedule']['backup']=xarML('Run Backup Job');

    if (xarModIsAvailable('scheduler')) {
        $data['intervals'] = xarModAPIFunc('scheduler','user','intervals');
        $data['interval'] = array();
        foreach ($schedulerapi as $func) {
            // see if we have a scheduler job running to execute this function
            $job = xarModAPIFunc('scheduler','user','get',
                                 array('module' => 'sitetools',
                                       'type' => 'scheduler',
                                       'func' => $func));
            if (empty($job) || empty($job['interval'])) {
                $data['interval'][$func] = '';
            } else {
                $data['interval'][$func] = $job['interval'];

            }
        }
    } else {
        $data['intervals'] = array();
        $data['interval'] = array();
    }

    $hooks = xarModCallHooks('module', 'modifyconfig', 'sitetools',
        array('module' => 'sitetools'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }

   /*Return the template variables defined in this function */
 return $data;
}
?>