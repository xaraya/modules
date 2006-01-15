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

 /*   if (!empty($scid)) {
        $scformdata=array();
        $scformdata=xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>(int)$scid));
        $data['sctypename']= ucfirst($scformdata[0]['sctypename']).' '.xarML('Form Settings');
        $data['customtext'] = $scformdata[0]['customtext'];
        $data['customtitle'] = $scformdata[0]['customtitle'];
        $data['optiontext'] = $scformdata[0]['optiontext'];
        $data['usehtmlemail']= (int)$scformdata[0]['usehtmlemail'];
        $data['allowcopy'] = (int)$scformdata[0]['allowcopy'];
        $data['webconfirmtext'] = $scformdata[0]['webconfirmtext'];
        $data['notetouser'] =  $scformdata[0]['notetouser'];
        $data['scdefaultemail']= $scformdata[0]['scdefaultemail'];
        $data['scdefaultname']= $scformdata[0]['scdefaultname'];
        $data['scid']=$scid;
    } else { // get global defaults */
       /* Specify some labels and values for display */
        $data['sctypename']= xarML('Global Configuration');
        $data['customtext']  = xarModGetVar('sitecontact', 'customtext');
        $data['customtitle'] = xarModGetVar('sitecontact', 'customtitle');
        $data['optiontext']  = xarModGetVar('sitecontact', 'optiontext');
        $data['usehtmlemail']= (int)xarModGetVar('sitecontact', 'usehtmlemail');
        $data['allowcopy']   = (int)xarModGetVar('sitecontact', 'allowcopy');
        $data['webconfirmtext'] = xarModGetVar('sitecontact', 'webconfirmtext');
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

    /* Get all the sitecontact forms now so we can choose a default */
    $scformdata=xarModAPIFunc('sitecontact','user','getcontacttypes');
    foreach ($scformdata as $k=>$scform) {
           $scforms[]=$scform;
    }
    $data['scforms']=$scforms;
/*
    if (!isset($scid)) {//let's use all for hooks for now
        $scid =null;
    } else {
*/
       // call modifyconfig hooks with module only for general hooks
       //  - not required for general config but leave for backward compatibility
        $hooks = xarModCallHooks('module', 'modifyconfig', 'sitecontact',
                             array('module'   => 'sitecontact'));

        if (empty($hooks)) {
            $data['hooks'] = array('dynamicdata' => xarML('You can add Dynamic Data fields here by hooking Dynamic Data to Sitecontact'));
        } else {
            $data['hooks'] = $hooks;
        }
//    }
    /* Do we need this here .. I don't think so */
    // Get the list of current hooks for item displays
    $hooklist = xarModGetHookList('sitecontact','item','display',0);
    $seenhook = array();
    foreach ($hooklist as $hook) {
        $seenhook[$hook['module']] = 1;
    }

    /* Create a link for each sitecontact form  */
    //Don't need this
/*    $scfilters = array();
    $scitem = array();

    // Link to default settings
    $scitem['sctitle'] = xarML('Default');
    $scitem['scid'] = 0;
    $scitem['sclink'] = xarModURL('sitecontact','admin','modifyconfig');
    $scfilters[] = $scitem;

    $data['sctypelink']=xarModURL('sitecontact','admin','managesctypes');
    // Links to settings per publication type
    foreach ($scformdata as $id => $scform) {
        $scitem['sclink'] = xarModURL('sitecontact','admin','modifyconfig',
                                         array('scid' => $scform['scid']));

        $scitem['sctitle'] = ucwords($scform['sctypename']);
        $scitem['scid'] = $scform['scid'];
        $scfilters[] = $scitem;
    }
    */
  //  $data['scfilters'] = $scfilters;
    $data['authid'] = xarSecGenAuthKey();
//    if (!isset($scid)  || $scid < 1) {
        $data['link'] = xarModURL('sitecontact','admin','updateconfig');
  /*  } else {
        $data['link'] = xarModURL('sitecontact','admin','managesctypes',
                                 array('action' => 'update'));
    }
  */
    /* Return the template variables defined in this function */
    return $data;
}
?>