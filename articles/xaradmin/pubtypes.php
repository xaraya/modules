<?php

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


    // Publication types can only be managed with ADMIN rights
    if (empty($ptid)) {
        $ptid = '';
        if (!xarSecurityCheck('AdminArticles')) return;
    } else {
        if (!xarSecurityCheck('AdminArticles',1,'Article',"$ptid:All:All:All")) return;
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
            }
            foreach ($input as $field => $value) {
                $config[$field]['input'] = $value;
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
                $settings = array('number_of_columns'    => 0,
                                  'itemsperpage'         => 20,
                                  'defaultview'          => 1,
                                  'showcategories'       => 1,
                                  'showprevnext'         => 0,
                                  'showcomments'         => 1,
                                  'showhitcounts'        => 1,
                                  'showratings'          => 0,
                                  'showarchives'         => 1,
                                  'showmap'              => 1,
                                  'showpublinks'         => 0,
                                  'dotransform'          => 0,
                                  'prevnextart'          => 0,
                                  'usealias'             => 0,
                                  'page_template'        => '');
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
            }
            foreach ($input as $field => $value) {
                $config[$field]['input'] = $value;
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
                // Redirect to the admin view page
                xarSessionSetVar('statusmsg',
                                xarML('Publication type updated'));
                xarResponseRedirect(xarModURL('articles', 'admin', 'pubtypes',
                                              array('action' => 'view')));
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
    }
    $data['pubtypes'] = $pubtypes;
    $data['newurl'] = xarModURL('articles',
                               'admin',
                               'pubtypes',
                               array('action' => 'new'));
    $data['newtitle'] = xarML('New');

    // Get the list of defined field formats
    $pubfieldformats = xarModAPIFunc('articles','user','getpubfieldformats');
    $data['formats'] = array();
    foreach ($pubfieldformats as $fname => $flabel) {
        $data['formats'][] = array('fname' => $fname, 'flabel' => $flabel);
    }

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
                                      'type'   => $pubfieldtypes[$field],
                                      'input'  => !empty($value['input']) ? 'checked ' : '');
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
                                      'type'   => $pubfieldtypes[$field],
                                      'input'  => !empty($value['input']) ? 'checked ' : '');
        }
    } elseif ($action == 'delete') {
        $data['item'] = $pubtypes[$ptid];
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Delete');
        $data['link'] = xarModURL('articles','admin','pubtypes',
                                 array('action' => 'confirm'));
    }

    $data['action'] = $action;
    $data['ptid'] = $ptid;

/* // dump the current configuration in xarinit.php format
$dump = "\n\n";
foreach ($pubtypes as $id => $val) {
    $name = $val['name'];
    $dump .= "    \$config['$name'] = array(\n";
    $config = $val['config'];
    foreach ($config as $field => $value) {
        $dump .= "        '$field' => array('label'  => ";
        if (empty($value['label'])) {
            $dump .= "'',\n";
        } else {
            $dump .= "xarML('" . $value['label'] . "'),\n";
        }
        $dump .= "                         'format' => '" . $value['format'] . "',\n";
        $dump .= "                         'input'  => ";
        if (empty($value['input'])) {
            $dump .= "0),\n";
        } else {
            $dump .= "1),\n";
        }
    }
    $dump .= "    );\n";
}
$data['status'] .= $dump;
*/


    // Return the template variables defined in this function
    return $data;
}

?>
