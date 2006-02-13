<?php
/**
 * Legis Master Document Management
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * manage master item types
 */
function legis_admin_masters()
{
    // Get parameters
    if (!xarVarFetch('mdid',   'isset', $mdid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('mdname', 'isset', $mdname, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('mdorder','isset', $mdorder, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('mddef',  'isset', $mddef,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('doclets',  'isset', $doclets,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('action', 'isset', $action,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('defaultmaster', 'isset', $defaultmaster,  NULL, XARVAR_DONT_SET)) {return;}
    //if (!xarVarFetch('oldstart',  'isset', $oldstart,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarSecurityCheck('AdminLegis')) return;
    if (empty($mdid)) {
        $mdid = '';

    }

    // Initialise the template variables
    $data = array();
    $data['mastertypes'] = array();

    // Get current item types
    $mastertypes = xarModAPIFunc('legis','user','getmastertypes');

    // Verify the action
    if (!isset($action) ||
       ($action != 'new' && $action != 'create'&& (empty($mdid) || !isset($mastertypes[$mdid])))
        ) {
        $action = 'view';
    }

    // Take action if necessary
    if ($action == 'create' || $action == 'update' || $action == 'confirm') {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;


        if ($action == 'create') {
            $mddefnew=serialize($mddef);
            $mdid = xarModAPIFunc('legis',
                                     'admin',
                                     'createmastertype',
                                 array('mdname' => $mdname,
                                       'mdorder'=> $mdorder,
                                       'mddef'  => $mddefnew));

            if (empty($mdid)) {
                return; // throw back
            } else {

                $settings = array('itemsperpage'         => 20,
                                  'usealias'             => 0,
                                  'defaultstatus'        => 3);
                xarModSetVar('legis', 'settings.'.$mdid,serialize($settings));
                xarModSetVar('legis', 'number_of_categories.'.$mdid, 0);
                xarModSetVar('legis', 'mastercids.'.$mdid, '');

                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',
                                xarML('Mastertype created'));
                xarResponseRedirect(xarModURL('legis', 'admin', 'masters',
                                              array('action' => 'view',
                                                    'mdid' => $mdid)));
                return true;
            }
        } elseif ($action == 'update') {
            $mddefnew=serialize($mddef);
               if (!xarModAPIFunc('legis',
                                'admin',
                                'updatemastertype',
                              array('mdid' => $mdid,
                                    'mdname' => $mdname,
                                    'mdorder' => $mdorder,
                                    'mddef' => $mddefnew))) {
                return; // throw back
            } else {

                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',
                                xarML('Master Doc type updated'));
                xarResponseRedirect(xarModURL('legis', 'admin', 'masters',
                                              array('action' => 'view', 'mdid'=>$mdid)));
                return true;
            }
        } elseif ($action == 'confirm') {
            if (!xarModAPIFunc('legis',
                              'admin',
                              'deletemastertype',
                              array('mdid' => $mdid))) {
                return; // throw back
            } else {
                xarModDelVar('legis', 'settings.'.$mdid);
                xarModDelAlias($mastertypes[$mdid]['mdname'],'legis');
   
                if ($mdid == $default) {
                    xarModSetVar('legis','defaultmastertype','');
                }

                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',
                                xarML('Master Doc type deleted'));
                xarResponseRedirect(xarModURL('legis', 'admin', 'masters',
                                              array('action' => 'view')));
                return true;
            }
        }
    }

    // Create Edit/Delete links
    foreach ($mastertypes as $id => $mastertype) {
        if (!xarSecurityCheck('AdminLegis',0)) {
            $mastertypes[$id]['editurl'] = '';
            $mastertypes[$id]['deleteurl'] = '';
            continue;
        }
        $mastertypes[$id]['editurl'] = xarModURL('legis',
                                             'admin',
                                             'masters',
                                             array('mdid' => $id,
                                                   'action' => 'modify'));
        $mastertypes[$id]['deleteurl'] = xarModURL('legis',
                                               'admin',
                                               'masters',
                                               array('mdid' => $id,
                                                     'action' => 'delete'));

    }
    $data['mastertypes'] = $mastertypes;
    $data['newurl'] = xarModURL('legis',
                               'admin',
                               'masters',
                               array('action' => 'new'));

    // Fill in relevant variables
    if ($action == 'new') {
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Create');
        $data['mdorder']=0;
        $docletlist =xarModAPIFunc('legis','user','getdoclets');
          if (is_array($docletlist)) {
            $doclets=array();
            foreach ($docletlist as $docid=>$doclet) {
                    $doclets[$docid]=$doclet['dlabel'];
            }
        }
        $data['doclets']=$doclets;
        $data['mddef']='';
        $data['link'] = xarModURL('legis','admin','masters',
                                 array('action' => 'create'));


    } elseif ($action == 'modify') {
        $data['item'] = $mastertypes[$mdid];
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Modify');
        $data['link'] = xarModURL('legis','admin','masters',
                                 array('action' => 'update'));
        $masterdef =xarModAPIFunc('legis','user','getmaster',array('mdid'=>$data['item']['mdid']));
        $docletlist =xarModAPIFunc('legis','user','getdoclets');
        $data['aredoclets']=trim($masterdef['mddef']);
        if ($data['aredoclets']<>'') {
            $currentdoclets = unserialize($masterdef['mddef']);

        } else {
            $currentdoclets=array();
        }
        if (is_array($docletlist)) {
            $doclets=array();
            foreach ($docletlist as $docid=>$doclet) {
                $doclets[$docid]['did']=$doclet['did'];
                $doclets[$docid]['doclabel']=$doclet['dlabel'];
                $doclets[$docid]['checked']=false;
                foreach ($currentdoclets as $currentid=>$currentdoc) {
                   if (in_array($doclets[$docid]['did'],$currentdoclets)) {
                        $doclets[$docid]['checked']=true;
                   } else{
                        $doclets[$docid]['checked']=false;
                  }
                }
            }
        }
 
        $data['doclets']=$doclets;

    } elseif ($action == 'delete') {
        $data['item'] = $mastertypes[$mdid];
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Delete');
        $data['numitems'] = xarModAPIFunc('legis','user','countitems',
                                          array('mdid' => $mdid));
        $data['link'] = xarModURL('legis','admin','masters',
                                 array('action' => 'confirm'));
    }


    $data['action'] = $action;
    $data['mdid'] = $mdid;

     $data['mastertypelink'] = xarModURL('legis','admin','masters');
    // Return the template variables defined in this function
    return $data;
}

?>
