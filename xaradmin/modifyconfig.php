<?php
/**
 * Modify the configuration settings
 * 
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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

       /* Specify some labels and values for display */
        $data['sctypename']= xarML('Global Configuration');
        $data['customtext']  = xarModGetVar('sitecontact', 'customtext');
        $data['customtitle'] = xarModGetVar('sitecontact', 'customtitle');
        $data['optiontext']  = xarModGetVar('sitecontact', 'optiontext');
        $data['usehtmlemail']= (int)xarModGetVar('sitecontact', 'usehtmlemail');
        $data['allowcopy']   = (int)xarModGetVar('sitecontact', 'allowcopy');
        $data['webconfirmtext'] = xarModGetVar('sitecontact', 'webconfirmtext');
        $data['savedata']   = xarModGetVar('sitecontact', 'savedata');
        $data['termslink']   = xarModGetVar('sitecontact', 'termslink'); 
        $soptions   = xarModGetVar('sitecontact', 'soptions');
        $data['permissioncheck']   = xarModGetVar('sitecontact', 'permissioncheck');
        if (!isset($soptions)) $soptions=array();

        $soptions=unserialize($soptions);
        if (is_array($soptions)) {
            foreach ($soptions as $k=>$v) {
                $data[$k]=$v;
            }
        }
        if (!isset($data['allowbcc']))$data['allowbcc']=false;
        if (!isset($data['allowcc']))$data['allowcc']=false;
        if (!isset($data['allowanoncopy']))$data['allowanoncopy']=false;        
        $notetouser = xarModGetVar('sitecontact', 'notetouser');
        if (!isset($notetouser) || (trim($notetouser)=='')) {
            $notetouser=xarModGetVar('sitecontact','defaultnote');
        }
        $data['notetouser']=$notetouser;

        $scdefaultemail = xarModGetVar('sitecontact', 'scdefaultemail');

        if (!isset($scdefaultemail) || (trim($scdefaultemail)=='')) {
            $scdefaultemail=xarModGetVar('mail','adminmail');
        }
        $data['scdefaultemail']= $scdefaultemail;

       $scdefaultname = xarModGetVar('sitecontact', 'scdefaultname');

       if (!isset($scdefaultname) || ($scdefaultname)=='') {
          $scdefaultname=xarModGetVar('mail','adminname');
       }
       $data['scdefaultname']= $scdefaultname;

//    }
    /* global config options */
    $data['shorturlschecked'] = xarModGetVar('sitecontact', 'SupportShortURLs') ? 'checked' : '';
    $data['formisactive'] = xarModGetVar('sitecontact', 'scactive') ? 'checked' : '';
    $data['scdefaultform']= xarModGetVar('sitecontact', 'defaultform');
    $data['itemsperpage']=  xarModGetVar('sitecontact', 'itemsperpage');
    $data['useModuleAlias']=xarModGetVar('sitecontact', 'useModuleAlias');
    $data['aliasname']=xarModGetVar('sitecontact', 'aliasname');
    $data['defaultsort'] = xarModGetVar('sitecontact', 'defaultsort');
    $data['useantibot'] = xarModGetVar('sitecontact', 'useantibot');
    
    /* Get all the sitecontact forms now so we can choose a default */
    $scformdata=xarModAPIFunc('sitecontact','user','getcontacttypes');
    foreach ($scformdata as $k=>$scform) {
           $scforms[]=$scform;
    }
    $data['scforms']=$scforms;
    
    // call modifyconfig hooks with module only for general hooks
    //  - not required for general config but leave for backward compatibility
        $hooks = xarModCallHooks('module', 'modifyconfig', 'sitecontact',
                           array('module'   => 'sitecontact'));

        if (empty($hooks)) {
            $data['hooks'] = array('dynamicdata' => xarML('You can add Dynamic Data fields here by hooking Dynamic Data to Sitecontact'));
        } else {
            $data['hooks'] = $hooks;
        }

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