<?php
/**
 * Legis Doclet Management
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
 * manage doclet types
 */
function legis_admin_doclets()
{
    // Get parameters
    if (!xarVarFetch('did',   'int:0:', $did,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('dname', 'str:1:', $dname, $dname, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('dlabel', 'str:1:', $dlabel,  $dlabel, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('dlabel2','str:1:', $dlabel2, $dlabel2, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('ddef',  'isset', $ddef,   $ddef, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('action', 'isset', $action,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('defaultdoclet', 'isset', $defaultdoclet,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarSecurityCheck('AdminLegis')) return;
    if (empty($did)) {
        $did = '';

    }

    // Initialise the template variables
    $data = array();
    $data['doclets'] = array();

    // Get current doclets
    $doclets = xarModAPIFunc('legis','user','getdoclets');

    // Verify the action
    if (!isset($action) ||
         ($action != 'new'
           && $action != 'create'
           && (empty($did) || !isset($doclets[$did])))
        ) {
        $action = 'view';
    }

    // Take action if necessary
    if ($action == 'create' || $action == 'update' || $action == 'confirm') {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;


        if ($action == 'create') {
            $config = array();
            $did = xarModAPIFunc('legis',
                                     'admin',
                                     'createdoclet',
                                 array('dname' => $dname,
                                       'dlabel'=> $dlabel,
                                       'dlabel2'=> $dlabel2,
                                       'ddef'  => $ddef));

            if (empty($did)) {
                return; // throw back
            } else {

                $settings = array('itemsperpage'         => 20,
                                  'usealias'             => 0,
                                  'defaultstatus'        => 3);
                xarModSetVar('legis', 'settings.'.$did,serialize($settings));
                xarModSetVar('legis', 'number_of_categories.'.$did, 0);
                xarModSetVar('legis', 'docletcids.'.$did, '');

                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',
                                xarML('Doclet created'));
                xarResponseRedirect(xarModURL('legis', 'admin', 'doclets',
                                              array('action' => 'view',
                                                    'did' => $did)));
                return true;
            }
        } elseif ($action == 'update') {

               if (!xarModAPIFunc('legis',
                                'admin',
                                'updatedoclet',
                              array('did' => $did,
                                    'dname' => $dname,
                                    'dlabel'=> $dlabel,
                                    'dlabel2'=> $dlabel2,                                    
                                    'ddef' => $ddef))) {
                return; // throw back
            } else {

                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',
                                xarML('Doclet updated'));
                xarResponseRedirect(xarModURL('legis', 'admin', 'doclets',
                                              array('action' => 'view', 'did'=>$did)));
                return true;
            }
        } elseif ($action == 'confirm') {
            if (!xarModAPIFunc('legis',
                              'admin',
                              'deletedoclet',
                              array('did' => $did))) {
                return; // throw back
            } else {
                xarModDelVar('legis', 'settings.'.$did);
                xarModDelAlias($doclets[$did]['dname'],'legis');
   
                if ($did == $default) {
                    xarModSetVar('legis','defaultdoclet','');
                }

                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',
                                xarML('Doclet deleted'));
                xarResponseRedirect(xarModURL('legis', 'admin', 'doclets',
                                              array('action' => 'view')));
                return true;
            }
        }
    }

    // Create Edit/Delete links
    foreach ($doclets as $id => $doclettype) {
        if (!xarSecurityCheck('AdminLegis',0)) {
            $doclets[$id]['editurl'] = '';
            $doclets[$id]['deleteurl'] = '';
            continue;
        }
        $doclets[$id]['editurl'] = xarModURL('legis',
                                             'admin',
                                             'doclets',
                                             array('did' => $id,
                                                   'action' => 'modify'));
        $doclets[$id]['deleteurl'] = xarModURL('legis',
                                               'admin',
                                               'doclets',
                                               array('did' => $id,
                                                     'action' => 'delete'));

    }
    $data['doclets'] = $doclets;
    $data['newurl'] = xarModURL('legis',
                               'admin',
                               'doclets',
                               array('action' => 'new'));

    // Fill in relevant variables
    if ($action == 'new') {
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Create');
        $data['ddef']='';
        $data['link'] = xarModURL('legis','admin','doclets',
                                 array('action' => 'create'));


    } elseif ($action == 'modify') {
        $data['item'] = $doclets[$did];

        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Modify');
        $data['link'] = xarModURL('legis','admin','doclets',
                                 array('action' => 'update'));

    } elseif ($action == 'delete') {
        $data['item'] = $doclets[$did];
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Delete');
        $data['numitems'] = xarModAPIFunc('legis','user','countitems',
                                          array('did' => $did));
        $data['link'] = xarModURL('legis','admin','doclets',
                                 array('action' => 'confirm'));
    }

    $data['action'] = $action;
    $data['did'] = $did;

     $data['docletlink'] = xarModURL('legis','admin','doclets');
    // Return the template variables defined in this function
    return $data;
}

?>
