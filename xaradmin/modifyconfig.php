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
    $data['adopath']     = xarModVars::get('sitetools','adocachepath');
    $data['rsspath']     = xarModVars::get('sitetools','rsscachepath');
    $data['templpath']   = xarModVars::get('sitetools','templcachepath');
    $data['backuppath']  = xarModVars::get('sitetools','backuppath');
    $data['usetimestamp']= xarModVars::get('sitetools','timestamp');
    $data['lineterm']    = xarModVars::get('sitetools','lineterm');
    $data['colnumber']    = xarModVars::get('sitetools','colnumber');
    $data['defaultbktype'] = xarModVars::get('sitetools','defaultbktype');
    $data['defaultbktype'] = xarModVars::get('sitetools','defaultbktype');
    $data['usedbprefix']    = xarModVars::get('sitetools','usedbprefix');

    $data['defadopath']   = sys::varpath()."/cache/adodb";
    $data['defrsspath']   = sys::varpath()."/cache/rss";
    $data['deftemplpath'] = sys::varpath()."/cache/templates";

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