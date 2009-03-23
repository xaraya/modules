<?php
/**
 * Modify the configuration settings
 * 
 * @package Xaraya
 * @copyright (C) 2004-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami SiteContact Module
 * @copyright (C) 2007,2008,2009 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
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
        $data['usehtmlemail']= (int)xarModGetVar('sitecontact', 'usehtmlemail')? true : false;
        $data['allowcopy']   = (int)xarModGetVar('sitecontact', 'allowcopy');
        $data['webconfirmtext'] = xarModGetVar('sitecontact', 'webconfirmtext');
        $data['savedata']   = xarModGetVar('sitecontact', 'savedata')? true : false;
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

        $data['fieldconfig'] =isset($data['fieldconfig'])?$data['fieldconfig']:'';        
        if (!isset($data['allowbcc']))$data['allowbcc']=false;
        if (!isset($data['allowcc']))$data['allowcc']=false;
        if (!isset($data['adminccs']))$data['adminccs']=false;
        if (!isset($data['admincclist']))$data['admincclist']='';
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
        //options for checkbox list fieldconfig
        $data['fieldarray'] = array(
                        'useremail'     =>xarML('Email'),
                        'username'      =>xarML('Username'),
                        'requesttext'   =>xarML('Subject'),
                        'company'       =>xarML('Organization'),
                        'usermessage'   =>xarML('Message'),
                        'ccrecipients'  =>xarML('CC List'),
                        'bccrecipients' =>xarML('BC List')
                        );
//    }
    /* global config options */
    $data['shorturls'] = xarModGetVar('sitecontact', 'SupportShortURLs') ? true : false;
    $data['scactive'] =xarModGetVar('sitecontact', 'scactive')? true : false;
    $data['scdefaultform']= xarModGetVar('sitecontact', 'defaultform');
    $data['itemsperpage']=  xarModGetVar('sitecontact', 'itemsperpage');
    $data['useModuleAlias']=xarModGetVar('sitecontact', 'useModuleAlias')? true : false;
    $data['aliasname']=xarModGetVar('sitecontact', 'aliasname');
    $data['defaultsort'] = xarModGetVar('sitecontact', 'defaultsort');
    $data['useantibot'] = xarModGetVar('sitecontact', 'useantibot')? true : false;
    $data['aliasname'] =xarModGetVar('sitecontact', 'aliasname');


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
    //common menulink
    $data['menulinks'] = xarModAPIFunc('sitecontact','admin','getmenulinks');
        $data['link'] = xarModURL('sitecontact','admin','updateconfig');
    /* Return the template variables defined in this function */
    return $data;
}
?>