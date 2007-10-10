<?php
/**
 * Modify the configuration settings
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * This is a standard function to modify the global configuration parameters of the
 * module
 * @author Jo Dalle Nogare
 */
function sitecontact_admin_modifyconfig()
{
    if (!xarSecurityCheck('AdminSiteContact')) return;

    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'sitecontact_general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'sitecontact', XARVAR_NOT_REQUIRED)) return;

    sys::import('xaraya.structures.hooks.observer');
    $subject = new HookSubject('sitecontact');
    $messenger = $subject->getMessenger();
    $messenger->setHook('module', 'getconfig');

    $hooks = $subject->notify();
    if (!empty($hooks) && isset($hooks['tabs'])) {
        foreach ($hooks['tabs'] as $key => $row) {
            $configarea[$key]  = $row['configarea'];
            $configtitle[$key] = $row['configtitle'];
            $configcontent[$key] = $row['configcontent'];
        }
        array_multisort($configtitle, SORT_ASC, $hooks['tabs']);
    } else {
        $hooks['tabs'] = array();
    }
    $data['hooks'] = $hooks;
    $data['tabmodule'] = $tabmodule;

    if ($tabmodule == 'sitecontact') {
        $soptions = xarModVars::get('sitecontact','soptions');
    } else {
        $regid = xarModGetIDFromName($tabmodule);
        $soptions = xarModGetUserVar('sitecontact','soptions', $regid);
    }
    if (!isset($soptions)) $soptions=array();
    
    $soptions=unserialize($soptions);
    if (is_array($soptions)) {
        foreach ($soptions as $k=>$v) {
            unset($data[$k]);
            $data[$k]=$v; 
        }
    }
    if (!isset($data['allowbcc']))$data['allowbcc']=false;
    if (!isset($data['allowcc']))$data['allowcc']=false;
    if (!isset($data['allowanoncopy']))$data['allowanoncopy']=false;          
 
   /* Specify some labels and values for display */
   /* not used?
    $soptions   = xarModVars::get('sitecontact', 'soptions');
    if (!isset($soptions)) $soptions=array();

    $soptions=unserialize($soptions);
    if (is_array($soptions)) {
        foreach ($soptions as $k=>$v) {
            $data[$k]=$v;
        }
    }
*/
    /* global config options */
// not used?    $data['defaultsort'] = xarModVars::get('sitecontact', 'defaultsort');

    /* Get all the sitecontact forms now so we can choose a default */
    $scformdata=xarModAPIFunc('sitecontact','user','getcontacttypes');
    $scforms = array();
    foreach ($scformdata as $k=>$scform) {
           $scforms[] = array('id' =>$scform['scid'], 'name' => ucfirst($scform['sctypename']));
    }
    $data['scforms'] = $scforms;

    /* Do we need this here .. I don't think so */
    // Get the list of current hooks for item displays
    $hooklist = xarModGetHookList('sitecontact','item','display',0);
    $seenhook = array();
    foreach ($hooklist as $hook) {
        $seenhook[$hook['module']] = 1;
    }

    $data['authid'] = xarSecGenAuthKey();

        $data['link'] = xarModURL('sitecontact','admin','updateconfig');
    /* Return the template variables defined in this function */
    return $data;
}
?>