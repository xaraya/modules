<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * manage publication types (all-in-one function for now)
 */
sys::import('xaraya.structures.query');

function publications_admin_pubtypes()
{
    if (!xarSecurityCheck('AdminPublications')) return;
    /*
    $myobject = DataObjectMaster::getObjectList(array('name' => 'objects'));
    $conditions = new Query();
    $conditions->eq('object_moduleid',xarMod::getRegID('publications'));
    $return_url = xarServerGetCurrentURL();

    return array('return_url'=>$return_url, 'object'=>$myobject, 'conditions' => $conditions);
    */

    // Get parameters
    if (!xarVarFetch('ptid',   'isset', $ptid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('action', 'isset', $action, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('name',   'isset', $name,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('descr',  'isset', $descr,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('label',  'isset', $label,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('format', 'isset', $format, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('input',  'isset', $input,  array(), XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('validation',  'isset', $validation,  NULL, XARVAR_DONT_SET)) {return;}


    // Publication types can only be managed with ADMIN rights
    if (empty($ptid)) {
        $ptid = '';
        if (!xarSecurityCheck('AdminPublications')) return;
    } else {
        if (!xarSecurityCheck('AdminPublications',1,'Publication',"$ptid:All:All:All")) return;
    }
    if (!isset($action)) {
        xarSession::setVar('statusmsg', '');
    }
    // Initialise the template variables
    $data = array();
    $data['pubtypes'] = array();

    // Get current publication types
    $pubtypes = xarModAPIFunc('publications','user','getpubtypes');

    // Verify the action
    if (!isset($action) || ($action != 'new' && $action != 'create' &&
                            (empty($ptid) || !isset($pubtypes[$ptid])))) {
        $action = 'view';
    }

    // Take action if necessary
    if ($action == 'create' || $action == 'update' || $action == 'confirm') {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        if ($action == 'create') {
            $config = array();
            foreach ($label as $field => $value) {
                $config[$field]['label'] = $value;
            }
            foreach ($format as $field => $value) {
                $config[$field]['format'] = $value;
                // some default basedirs for now...
                if (isset($validation[$field])) {
                    $config[$field]['validation'] = $validation[$field];
                } elseif ($value == 'imagelist') {
                    $config[$field]['validation'] = 'modules/publications/xarimages';
                } elseif ($value == 'webpage') {
                    $config[$field]['validation'] = 'modules/publications';
                }
            }
            foreach ($input as $field => $value) {
                $config[$field]['input'] = 1;
            }
            $ptid = xarModAPIFunc('publications',
                                 'admin',
                                 'createpubtype',
                                 array('name' => $name,
                                       'descr' => $descr,
                                       'config' => $config));
            if (empty($ptid)) {
                return; // throw back
            } else {
                if (empty($config['state']['label'])) {
                    $state = 2;
                } else {
                    $state = 0;
                }
                $settings = array('number_of_columns'    => 0,
                                  'itemsperpage'         => 20,
                                  'defaultview'          => 1,
                                  'showcategories'       => 1,
                                  'showcatcount'         => 0,
                                  'showprevnext'         => 0,
                                  'showcomments'         => 1,
                                  'showhitcounts'        => 1,
                                  'showratings'          => 0,
                                  'showarchives'         => 1,
                                  'showmap'              => 1,
                                  'showpublinks'         => 0,
                                  'showpubcount'         => 0,
                                  'dotransform'          => 0,
                                  'titletransform'       => 0,
                                  'prevnextart'          => 0,
                                  'usealias'             => 0,
                                  'page_template'        => '',
                                  'usetitleforurl'       => 0,
                                  'defaultstate'        => $state,
                                  'defaultsort'          => 'date');
                xarModVars::set('publications', 'settings.'.$ptid,serialize($settings));
                xarModVars::set('publications', 'number_of_categories.'.$ptid, 0);
                xarModVars::set('publications', 'mastercids.'.$ptid, '');

                // Redirect to the admin view page
                xarSession::setVar('statusmsg',
                                xarML('Publication type created'));
                xarResponseRedirect(xarModURL('publications', 'admin', 'pubtypes',
                                              array('action' => 'view')));
                return true;
            }
        } elseif ($action == 'update') {
            $config = array();
            foreach ($label as $field => $value) {
                $config[$field]['label'] = $value;
            }
            foreach ($format as $field => $value) {
                $config[$field]['format'] = $value;
                // some default basedirs for now...
                if (isset($validation[$field])) {
                    $config[$field]['validation'] = $validation[$field];
                } elseif ($value == 'imagelist') {
                    $config[$field]['validation'] = 'modules/publications/xarimages';
                } elseif ($value == 'webpage') {
                    $config[$field]['validation'] = 'modules/publications';
                }
            }
            foreach ($input as $field => $value) {
                $config[$field]['input'] = 1;
            }
            if (!xarModAPIFunc('publications',
                              'admin',
                              'updatepubtype',
                              array('ptid' => $ptid,
                              //      'name' => $name, /* not allowed here */
                                    'descr' => $descr,
                                    'config' => $config))) {
                return; // throw back
            } else {
                // Redirect back to the admin modify page to continue editing publication type
                xarSession::setVar('statusmsg',
                                xarML('Publication type updated'));
                xarResponseRedirect(xarModURL('publications', 'admin', 'pubtypes',array('ptid'=>$ptid,'action' => 'modify')));
                return true;
            }
        } elseif ($action == 'confirm') {
        // TODO: clean up more stuff here, like publications etc. ?
            if (!xarModAPIFunc('publications',
                              'admin',
                              'deletepubtype',
                              array('ptid' => $ptid))) {
                return; // throw back
            } else {
                xarModVars::delete('publications', 'settings.'.$ptid);
                xarModDelAlias($pubtypes[$ptid]['name'],'publications');
                xarModVars::delete('publications', 'number_of_categories.'.$ptid);
                xarModVars::delete('publications', 'mastercids.'.$ptid);
                $default = xarModVars::get('publications','defaultpubtype');
                if ($ptid == $default) {
                    xarModVars::set('publications','defaultpubtype','');
                }

                // Redirect to the admin view page
                xarSession::setVar('statusmsg',
                                xarML('Publication type deleted'));
                xarResponseRedirect(xarModURL('publications', 'admin', 'pubtypes',
                                              array('action' => 'view')));
                return true;
            }
        }
    }

    // Create Edit/Delete links
    foreach ($pubtypes as $id => $pubtype) {
        if (!xarSecurityCheck('AdminPublications',0,'Publication',"$id:All:All:All")) {
            $pubtypes[$id]['editurl'] = '';
            $pubtypes[$id]['deleteurl'] = '';
            $pubtypes[$id]['configurl'] = '';
            $pubtypes[$id]['viewurl'] = '';
            $pubtypes[$id]['addurl'] = '';
            continue;
        }
        $pubtypes[$id]['editurl'] = xarModURL('publications',
                                             'admin',
                                             'pubtypes',
                                             array('ptid' => $id,
                                                   'action' => 'modify'));
        $pubtypes[$id]['deleteurl'] = xarModURL('publications',
                                               'admin',
                                               'pubtypes',
                                               array('ptid' => $id,
                                                     'action' => 'delete'));
        $pubtypes[$id]['configurl'] = xarModURL('publications',
                                               'admin',
                                               'modifyconfig',
                                               array('ptid' => $id));
        $pubtypes[$id]['viewurl'] = xarModURL('publications',
                                               'admin',
                                               'view',
                                               array('ptid' => $id));
        $pubtypes[$id]['addurl'] = xarModURL('publications',
                                               'admin',
                                               'new',
                                               array('ptid' => $id));
    }
    $data['pubtypes'] = $pubtypes;
    $data['newurl'] = xarModURL('publications',
                               'admin',
                               'pubtypes',
                               array('action' => 'new'));

/*
    // Get the list of defined field formats
    $pubfieldformats = xarModAPIFunc('publications','user','getpubfieldformats');
    $data['formats'] = array();
    foreach ($pubfieldformats as $fname => $flabel) {
        $data['formats'][] = array('fname' => $fname, 'flabel' => $flabel);
    }
*/
    // Fill in relevant variables
    if ($action == 'new') {
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Create');
        $data['link'] = xarModURL('publications','admin','pubtypes',
                                 array('action' => 'create'));

        $data['fields'] = array();
        $pubfieldtypes = xarModAPIFunc('publications','user','getpubfieldtypes');
        // Fill in the *default* configuration fields
        $pubfields = xarModAPIFunc('publications','user','getpubfields');
    } elseif ($action == 'modify') {
        $data['item'] = $pubtypes[$ptid];
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Modify');
        $data['link'] = xarModURL('publications','admin','pubtypes',
                                 array('action' => 'update'));

        $data['fields'] = array();
        $pubfieldtypes = xarModAPIFunc('publications','user','getpubfieldtypes');
        // Fill in the *current* configuration fields
    // TODO: make order dependent on pubtype or not ?
    //    foreach ($pubtypes[$ptid]['config'] as $field => $value) {
        $pubfields = xarModAPIFunc('publications','user','getpubfields');
    } elseif ($action == 'delete') {
        $data['item'] = $pubtypes[$ptid];
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Delete');
        $data['numitems'] = xarModAPIFunc('publications','user','countitems',
                                          array('ptid' => $ptid));
        $data['link'] = xarModURL('publications','admin','pubtypes',
                                 array('action' => 'confirm'));
    }

    $data['action'] = $action;
    $data['ptid'] = $ptid;

    // Return the template variables defined in this function
    return $data;
}

?>
