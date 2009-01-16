<?php
/**
 * Sitecontact itemtype management
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * manage sitecontact item types
 */
function sitecontact_admin_managesctypes()
{
    // Get parameters
    if(!xarVarFetch('scid',          'int:1:',   $scid,           NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('sctypename',    'str:1:',   $sctypename,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('sctypedesc',    'str:1:',   $sctypedesc,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('customtext',    'str:1:',   $customtext,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('customtitle',   'str:1:',   $customtitle,    '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('optiontext',    'str:1:',   $optiontext,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('webconfirmtext','str:1:',   $webconfirmtext, '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('notetouser',    'str:1:',   $notetouser,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('allowcopy',     'checkbox', $allowcopy,      true,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('usehtmlemail',  'checkbox', $usehtmlemail,   false,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('scdefaultemail','str:1:',   $scdefaultemail, '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('scdefaultname', 'str:1:',   $scdefaultname,  '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('action',       'isset',    $action,         NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('scactive',     'checkbox', $scactive,       true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('savedata',     'checkbox', $savedata, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('permissioncheck', 'checkbox', $permissioncheck, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('termslink',    'str:1:',   $termslink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowccs',      'checkbox', $allowccs, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowbccs',     'checkbox', $allowbccs, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowanoncopy', 'checkbox', $allowanoncopy, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('useantibot',    'checkbox', $useantibot,    true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum',      'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('EditSiteContact')) return;
    xarVarSetCached('sitecontact.data','scid',$scid);
    // Initialise the template variables
    $data = array();
    $sctypes=array();

    // Get current item types
    $sctypes = xarModAPIFunc('sitecontact','user', 'getcontacttypes',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('sitecontact','itemsperpage')));

    $defaultform= xarModGetVar('sitecontact','defaultform');
    $data['defaultform']=$defaultform;
    // Verify the action
    if (!isset($action) ){
        $action = 'view';
        xarSessionSetVar('statusmsg','');
    }
    if (!isset($scid) && $action =='view') {
         xarSessionSetVar('statusmsg','');
    }
    
    // Add a pager for forms
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('sitecontact','user','countitems'),
        xarModURL('sitecontact', 'admin', 'managesctypes', array('action'=>$action, 'startnum' => '%%')),
        xarModGetVar('sitecontact', 'itemsperpage'));


    $data['managetype']=xarML('List Forms');
    $formisactive = xarModGetVar('sitecontact', 'scactive') ? 'checked' : '';
    $allowanoncopy = ($allowcopy && $allowanoncopy)? true :false; //only allow anonymous if allow copy for registered too
    $soptions=array('allowccs'=>$allowccs,'allowbccs'=>$allowbccs, 'allowanoncopy' => $allowanoncopy, 'useantibot'=>$useantibot);
    $soptions=serialize($soptions);

    //Setup array with captured vars
    $item=array('scid' => (int)$scid,
                'sctypename'     => $sctypename,
                'sctypedesc'     => $sctypedesc,
                'customtext'     => $customtext,
                'customtitle'    => $customtitle,
                'optiontext'     => $optiontext,
                'webconfirmtext' => $webconfirmtext,
                'notetouser'     => $notetouser,
                'allowcopy'      => (int)$allowcopy,
                'usehtmlemail'   => (int)$usehtmlemail,
                'scdefaultemail' => $scdefaultemail,
                'scdefaultname'  => $scdefaultname,
                'scactive'       => (int)$scactive,
                'savedata'       => $savedata,
                'permissioncheck'=> $permissioncheck,
                'termslink'      => $termslink,
                'allowccs'       => $allowccs,
                'allowbccs'      => $allowbccs,
                'allowanoncopy'  => $allowanoncopy,
                'soptions'       => $soptions,
                'useantibot'     => $useantibot,
                'formisactive'   => $formisactive // add this in addition to normal field value
                );

    // Take action if necessary
    if ($action == 'create' || $action == 'update' || $action == 'confirm'){
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        if ($action == 'create') {
   
            $sctype = xarModAPIFunc('sitecontact','admin','createsctype',$item);

            if (isset($sctype) && $sctype['created']==1) {
               // Redirect to the admin view page
                xarSessionSetVar('statusmsg',xarML('New Sitecontact Form created'));
                xarResponseRedirect(xarModURL('sitecontact', 'admin', 'managesctypes',
                                              array('action' => 'view',
                                                    'scid' => $sctype['sctypeid'])));
                return true;
            } else {
                   xarSessionSetVar('statusmsg',xarML('Problem with creation of New Sitecontact Form '));
                   xarResponseRedirect(xarModURL('sitecontact', 'admin', 'managesctypes'));
            }

        } elseif ($action == 'update') {

             $updatedscid=xarModAPIFunc('sitecontact','admin','updatesctype', $item);

             if (!$updatedscid) {
                xarSessionSetVar('statusmsg',xarML('Problem updating Sitecontact form #(1)',$item['sctypename']));
                $msg = xarML('Problem updating a Sitecontact form with ID of #(1)',$item['scid']);
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
                return false;
            } else {

                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',xarML('Contact form updated'));
                xarResponseRedirect(xarModURL('sitecontact', 'admin', 'managesctypes',
                                              array('action' => 'view', 'scid'=>$scid)));
                return true;
            }

        } elseif ($action == 'confirm') { //go ahead and delete

           $item = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));

           $data['item']=$item[0];
           if ($scid == $defaultform) {
            $msg = xarML('You cannot delete the default form. Please change the default form first');
             xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
            return false;
           }
           if (!xarModAPIFunc('sitecontact','admin','deletesctype',array('scid'=> (int)$scid))) {
              $msg = xarML('Problem deleting a sitecontact Form with id #(1)',$scid);
             xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
            return false;
           } else {
                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',
                                xarML('Sitecontact Form deleted'));
                xarResponseRedirect(xarModURL('sitecontact', 'admin', 'managesctypes',
                                              array('action' => 'view')));
                return true;
          }
        }
    }


    // Create Edit/Delete links
    $totalforms=count($sctypes);

    if ($totalforms >0 ) {
      foreach ($sctypes as $id => $sctype) {
        if (xarSecurityCheck('EditSiteContact',0)) {
            $sctypes[$id]['editurl'] = xarModURL('sitecontact','admin','managesctypes',
                                             array('scid' => $sctype['scid'],
                                                   'action' => 'modify'));
        } else {
            $sctypes[$id]['editurl']='';
        }
        if (xarSecurityCheck('DeleteSiteContact',0)) {
            if (($totalforms >1)  && ($sctype['scid'] != $defaultform)){ //we can delete but not if only form left, or the default one
                $sctypes[$id]['deleteurl'] = xarModURL('sitecontact','admin','managesctypes',
                                               array('scid' => $sctype['scid'],
                                                     'action' => 'delete'));
            }

        } else {
               $sctypes[$sctype['scid']]['deleteurl'] = '';
        }

        if (xarSecurityCheck('EditSiteContact',0)) {
            $sctypes[$id]['previewurl'] = xarModURL('sitecontact','admin','managesctypes',
                                               array('scid' =>$sctype['scid'],
                                                     'action' => 'preview'));
        } else {
            $sctypes[$id]['previewurl'] = '';
        }
      }
    }

    $data['newurl'] = xarModURL('sitecontact','admin','managesctypes',
                               array('action' => 'new'));

    // Fill in relevant variables
    if ($action == 'new') {
        xarSessionSetVar('statusmsg','');
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Create');
        $data['managetype']=xarML('Create Form');
        $data['link'] = xarModURL('sitecontact','admin','managesctypes',
                                 array('action' => 'create'));
        $soptions =xarModGetVar('sitecontact','soptions');
        $soptions=unserialize($soptions);
        if (is_array($soptions)) {
            foreach ($soptions as $k=>$v) {
                $k=$v;
            }
        }
        if (!isset($allowbccs)) $allowbccs=false;
        if (!isset($allowccs)) $allowccs=false;
        if (!isset($allowanoncopy)) $allowanoncopy=false;   
        if (!isset($useantibot)) $useantibot=false;
        $item = array('sctypename'     => xarML('Unique name for new form'),
                      'sctypedesc'     => xarML('Another contact form'),
                      'customtext'     => xarModGetVar('sitecontact','customtext'),
                      'customtitle'    => xarModGetVar('sitecontact','customtitle'),
                      'optiontext'     => xarModGetVar('sitecontact','optiontext'),
                      'webconfirmtext' => xarModGetVar('sitecontact','webconfirmtext'),
                      'notetouser'     => xarModGetVar('sitecontact','notetouser'),
                      'allowcopy'      => xarModGetVar('sitecontact','allowcopy'),
                      'usehtmlemail'   => xarModGetVar('sitecontact','usehtmlemail'),
                      'scdefaultemail' => xarModGetVar('sitecontact','scdefaultemail'),
                      'scdefaultname'  => xarModGetVar('sitecontact','scdefaultname'),
                      'permissioncheck'=> xarModGetVar('sitecontact','permissioncheck'),
                      'savedata'       => xarModGetVar('sitecontact','savedata'),
                      'termslink'      => xarModGetVar('sitecontact','termslink'),
                      'allowbccs'      => $allowbccs,
                      'allowccs'       => $allowccs,
                      'allowanoncopy'  => $allowanoncopy,
                      'useantibot'     => $useantibot,
                      'formisactive'   => (xarModGetVar('sitecontact', 'scactive') ? 'checked' : '')
                );
        $data['item']=$item;

    } elseif ($action == 'modify') {
         xarSessionSetVar('statusmsg','');
        $item = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));
        $data['item']=$item[0];

        if (isset($data['item']['soptions'])) {
            $soptions=unserialize($data['item']['soptions']);
            if (is_array($soptions)) {
                foreach ($soptions as $k=>$v) {
                    $data['item'][$k]=$v;
                }
            }
        }

        if (!isset($data['item']['allowbccs']))$data['item']['allowbccs']=0;
        if (!isset($data['item']['allowccs']))$data['item']['allowccs']=0;
        if (!isset($data['item']['allowanoncopy']))$data['item']['allowanoncopy']=0;
        if (!isset($data['item']['useantibot']))$data['item']['useantibot']=false;
        if (!isset($data['item']['savedata']))$data['item']['savedata']=xarModGetVar('sitecontact','savedata')?xarModGetVar('sitecontact','savedata'):0;
        if (!isset($data['item']['permissioncheck']))$data['item']['permissioncheck']=xarModGetVar('sitecontact','permissioncheck');
        if (!isset($data['item']['termslink']))$data['item']['termslink']=xarModGetVar('sitecontact','termslink');

        $data['managetype']=xarML('Edit Form Definition');
        $data['formisactive']=xarModGetVar('sitecontact', 'scactive') ? 'checked' : '';
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Modify');
        $data['link'] = xarModURL('sitecontact','admin','managesctypes',
                                 array('action' => 'update'));
        $hooks = xarModCallHooks('module', 'modifyconfig', 'sitecontact',
                             array('module'   => 'sitecontact',
                                   'itemtype' => $scid));
         if (empty($hooks)) {
            $data['hooks'] = array('dynamicdata' => xarML('You can add Dynamic Data fields here by hooking Dynamic Data to Sitecontact'));
        } else {
            $data['hooks'] = $hooks;
        }
    } elseif ($action == 'delete') {
        xarSessionSetVar('statusmsg','');
        $item = xarModAPIFunc('sitecontact','user','getcontacttypes', array('scid'=> $scid));
        if (is_array($item) && count($item) == 1) {
          $data['item']=$item[0];
        } else {
          //there is something wrong - the item doesn't exist
            $msg = xarML('There has been an error. Please contact the system administrator and inform them of this error message.');
             xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
            return false;
        }
        if ($scid == $defaultform) {
            $msg = xarML('You cannot delete the default form. Please change the default form first');
             xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
            return false;
        }
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Delete');
        $data['managetype']=xarML('Delete Form Definition');
        $data['numitems'] = xarModAPIFunc('sitecontact','user','countitems');
        $data['link'] = xarModURL('sitecontact','admin','managesctypes',
                                 array('action' => 'confirm'));

    } elseif ($action == 'preview') {
        xarSessionSetVar('statusmsg','');
        $item = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));
        $data['item']=$item[0];
        if (isset($data['item']['soptions'])) {
            $soptions=unserialize($data['item']['soptions']);
            if (is_array($soptions)) {
                foreach ($soptions as $k=>$v) {
                   $data['item'][$k]=$v;
               }
            }
        }

        if (!isset($data['item']['allowbccs']))$data['item']['allowbccs']=0;
        if (!isset($data['item']['allowccs']))$data['item']['allowccs']=0;
        if (!isset($data['item']['allowanoncopy']))$data['item']['allowanoncopy']=0;
        if (!isset($data['item']['useantibot']))$data['item']['useantibot']=false;
        if (!isset($data['item']['savedata']))$data['item']['savedata']=xarModGetVar('sitecontact','savedata');
        if (!isset($data['item']['permissioncheck']))$data['item']['permissioncheck']=xarModGetVar('sitecontact','permissioncheck');
        if (!isset($data['item']['termslink']))$data['item']['termslink']=xarModGetVar('sitecontact','termslink');

        $optionset=explode(',',$item[0]['optiontext']);
        $data['optionset']=$optionset;
        $optionitems=array();
        foreach ($optionset as $optionitem) {
           $optionitems[]=explode(';',$optionitem);
        }

       $data['requesttext']='';
       $data['optionitems']=$optionitems;
       $data['link'] = xarModURL('sitecontact','admin','managesctypes');
       $scid=$data['item']['scid'];
       $data['managetype']=xarML('Preview Form');
       if (xarModIsHooked('dynamicdata','sitecontact',$scid) ) {
          /* get the Dynamic Object defined for this module (and itemtype, if relevant) */
          $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'sitecontact',
                                   'itemtype' => $scid));
          if (!isset($object)) return;  /* throw back */

         /* check the input values for this object and do ....what here? */
         $isvalid = $object->checkInput();

         /*we just want a copy of data - don't need to save it in a table (no request yet anyway!) */
         $dditems =& $object->getProperties();

         foreach ($dditems as $itemid => $fields) {
            $items[$itemid] = array();
            foreach ($fields as $name => $value) {
                $items[$itemid][$name] = ($value);
            }

            $propdata=array();
            foreach ($items as $key => $value) {
                $propdata[$value['name']]['label']=$value['label'];
                $propdata[$value['name']]['value']=$value['value'];
            }
         }
      }
    }

    $data['action'] = $action;
    $data['sctypes']=$sctypes;

    $data['sctypelink'] = xarModURL('sitecontact','admin','managesctypes');
    // Return the template variables defined in this function
    return $data;
}

?>
