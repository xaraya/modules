<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * manage publication types (all-in-one function for now)
 */
function articles_admin_pubtypes()
{
    // Get parameters
    if (!xarVarFetch('ptid',   'isset', $ptid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('action', 'isset', $action, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('name',   'isset', $name,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('descr',  'isset', $descr,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('label',  'isset', $label,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('format', 'isset', $format, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('input',  'isset', $input,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('validation',  'isset', $validation,  NULL, XARVAR_DONT_SET)) {return;}


    // Publication types can only be managed with ADMIN rights
    if (empty($ptid)) {
        $ptid = '';
        if (!xarSecurityCheck('AdminArticles')) return;
    } else {
        if (!xarSecurityCheck('AdminArticles',1,'Article',"$ptid:All:All:All")) return;
    }
    if (!isset($action)) {
        xarSessionSetVar('statusmsg', '');
    }
    // Initialise the template variables
    $data = array();
    $data['pubtypes'] = array();

    // Get current publication types
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

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
                    $config[$field]['validation'] = 'modules/articles/xarimages';
                } elseif ($value == 'webpage') {
                    $config[$field]['validation'] = 'modules/articles';
                }
            }
            foreach ($input as $field => $value) {
                $config[$field]['input'] = 1;
            }
            $ptid = xarModAPIFunc('articles',
                                 'admin',
                                 'createpubtype',
                                 array('name' => $name,
                                       'descr' => $descr,
                                       'config' => $config));
            if (empty($ptid)) {
                return; // throw back
            } else {
                if (empty($config['status']['label'])) {
                    $status = 2;
                } else {
                    $status = 0;
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
                                  'defaultstatus'        => $status,
                                  'defaultsort'          => 'date');
                xarModSetVar('articles', 'settings.'.$ptid,serialize($settings));
                xarModSetVar('articles', 'number_of_categories.'.$ptid, 0);
                xarModSetVar('articles', 'mastercids.'.$ptid, '');

                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',
                                xarML('Publication type created'));
                xarResponseRedirect(xarModURL('articles', 'admin', 'pubtypes',
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
                    $config[$field]['validation'] = 'modules/articles/xarimages';
                } elseif ($value == 'webpage') {
                    $config[$field]['validation'] = 'modules/articles';
                }
            }
            foreach ($input as $field => $value) {
                $config[$field]['input'] = 1;
            }
            if (!xarModAPIFunc('articles',
                              'admin',
                              'updatepubtype',
                              array('ptid' => $ptid,
                              //      'name' => $name, /* not allowed here */
                                    'descr' => $descr,
                                    'config' => $config))) {
                return; // throw back
            } else {
                // Redirect back to the admin modify page to continue editing publication type
                xarSessionSetVar('statusmsg',
                                xarML('Publication type updated'));
                xarResponseRedirect(xarModURL('articles', 'admin', 'pubtypes',array('ptid'=>$ptid,'action' => 'modify')));
                return true;
            }
        } elseif ($action == 'confirm') {
        // TODO: clean up more stuff here, like articles etc. ?
            if (!xarModAPIFunc('articles',
                              'admin',
                              'deletepubtype',
                              array('ptid' => $ptid))) {
                return; // throw back
            } else {
                xarModDelVar('articles', 'settings.'.$ptid);
                xarModDelAlias($pubtypes[$ptid]['name'],'articles');
                xarModDelVar('articles', 'number_of_categories.'.$ptid);
                xarModDelVar('articles', 'mastercids.'.$ptid);
                $default = xarModGetVar('articles','defaultpubtype');
                if ($ptid == $default) {
                    xarModSetVar('articles','defaultpubtype','');
                }

                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',
                                xarML('Publication type deleted'));
                xarResponseRedirect(xarModURL('articles', 'admin', 'pubtypes',
                                              array('action' => 'view')));
                return true;
            }
        }
    }

    // Create Edit/Delete links
    foreach ($pubtypes as $id => $pubtype) {
        if (!xarSecurityCheck('AdminArticles',0,'Article',"$id:All:All:All")) {
            $pubtypes[$id]['editurl'] = '';
            $pubtypes[$id]['deleteurl'] = '';
            $pubtypes[$id]['configurl'] = '';
            $pubtypes[$id]['viewurl'] = '';
            $pubtypes[$id]['addurl'] = '';
            continue;
        }
        $pubtypes[$id]['editurl'] = xarModURL('articles',
                                             'admin',
                                             'pubtypes',
                                             array('ptid' => $id,
                                                   'action' => 'modify'));
        $pubtypes[$id]['deleteurl'] = xarModURL('articles',
                                               'admin',
                                               'pubtypes',
                                               array('ptid' => $id,
                                                     'action' => 'delete'));
        $pubtypes[$id]['configurl'] = xarModURL('articles',
                                               'admin',
                                               'modifyconfig',
                                               array('ptid' => $id));
        $pubtypes[$id]['viewurl'] = xarModURL('articles',
                                               'admin',
                                               'view',
                                               array('ptid' => $id));
        $pubtypes[$id]['addurl'] = xarModURL('articles',
                                               'admin',
                                               'new',
                                               array('ptid' => $id));
    }
    $data['pubtypes'] = $pubtypes;
    $data['newurl'] = xarModURL('articles',
                               'admin',
                               'pubtypes',
                               array('action' => 'new'));

/*
    // Get the list of defined field formats
    $pubfieldformats = xarModAPIFunc('articles','user','getpubfieldformats');
    $data['formats'] = array();
    foreach ($pubfieldformats as $fname => $flabel) {
        $data['formats'][] = array('fname' => $fname, 'flabel' => $flabel);
    }
*/
    // Fill in relevant variables
    if ($action == 'new') {
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Create');
        $data['link'] = xarModURL('articles','admin','pubtypes',
                                 array('action' => 'create'));

        $data['fields'] = array();
        $pubfieldtypes = xarModAPIFunc('articles','user','getpubfieldtypes');
        // Fill in the *default* configuration fields
        $pubfields = xarModAPIFunc('articles','user','getpubfields');
        foreach ($pubfields as $field => $value) {
            $data['fields'][] = array('name'   => $field,
                                      'label'  => $value['label'],
                                      'format' => $value['format'],
                                      'validation' => !empty($value['validation']) ? $value['validation'] : '',
                                      'type'   => $pubfieldtypes[$field],
                                      'input'  => !empty($value['input']) ? 'checked="checked" ' : '');
        }
    } elseif ($action == 'modify') {
        $data['item'] = $pubtypes[$ptid];
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Modify');
        $data['link'] = xarModURL('articles','admin','pubtypes',
                                 array('action' => 'update'));

        $data['fields'] = array();
        $pubfieldtypes = xarModAPIFunc('articles','user','getpubfieldtypes');
        // Fill in the *current* configuration fields
    // TODO: make order dependent on pubtype or not ?
    //    foreach ($pubtypes[$ptid]['config'] as $field => $value) {
        $pubfields = xarModAPIFunc('articles','user','getpubfields');
        foreach ($pubfields as $field => $dummy) {
            $value = $pubtypes[$ptid]['config'][$field];
            $data['fields'][] = array('name'   => $field,
                                      'label'  => $value['label'],
                                      'format' => $value['format'],
                                      'validation' => !empty($value['validation']) ? $value['validation'] : '',
                                      'type'   => $pubfieldtypes[$field],
                                      'input'  => !empty($value['input']) ? 'checked="checked" ' : '');
        }
    } elseif ($action == 'delete') {
        $data['item'] = $pubtypes[$ptid];
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Delete');
        $data['numitems'] = xarModAPIFunc('articles','user','countitems',
                                          array('ptid' => $ptid));
        $data['link'] = xarModURL('articles','admin','pubtypes',
                                 array('action' => 'confirm'));
    }

    $data['action'] = $action;
    $data['ptid'] = $ptid;

    // Return the template variables defined in this function
    return $data;
}

?>
