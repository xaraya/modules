<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function commerce_admin_categories_screen()
{

    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cInfo',  'int',  $cID, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cID',    'int',  $data['cID'],   0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('cPath',  'str',  $data['cPath'], '', XARVAR_NOT_REQUIRED)) {return;}

    $languages = xarModAPIFunc('commerce','user','get_languages');
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $currentlang = xarModAPIFunc('commerce','user','get_language',array('locale' => $data['language']));
    $data['languages'] = $languages;
    $data['currentlang'] = $currentlang;
    if(!xarVarFetch('langid',    'int',  $data['langid'], $currentlang['id'], XARVAR_DONT_SET)) {return;}

    if (isset($action)) {
        switch ($action) {
            case 'insert_category':
            case 'update_category':
                if(!xarVarFetch('sort_order',    'str',  $sort_order, NULL, XARVAR_DONT_SET)) {return;}
                if(!xarVarFetch('categories_status',    'str',  $categories_status, NULL, XARVAR_DONT_SET)) {return;}
                if(!xarVarFetch('categories_name',    'array',  $categories_name, NULL, XARVAR_DONT_SET)) {return;}

                if (($edit_x) || ($edit_y)) {
                    $action = 'edit_category_ACD';
                }
                else {
                    if ($categories_id == '') {
                        $categories_id = $cID;
                    }
                    $q->addfield('sort_order',$sort_order);
                    $q->addfield('categories_status',$categories_status);

                    $q->addtable('commerce_categories');
                    if ($action == 'insert_category') {
                        $q->addfield('parent_id',$current_category_id);
                        $q->addfield('date_added',mktime());
//                        $categories_id = xtc_db_insert_id();
                    }
                    elseif ($action == 'update_category') {
                        $q->addfield('last_modified',mktime());
                        $q->eq('categories_id',$categories_id);
                    }

                    if(!xarVarFetch('categories_name',    'array',  $categories_name, NULL, XARVAR_DONT_SET)) {return;}
                    if(!xarVarFetch('categories_heading_title',    'array',  $categories_heading_title, NULL, XARVAR_DONT_SET)) {return;}
                    if(!xarVarFetch('categories_description',    'array',  $categories_description, NULL, XARVAR_DONT_SET)) {return;}
                    if(!xarVarFetch('categories_meta_title',    'array',  $categories_meta_title, NULL, XARVAR_DONT_SET)) {return;}
                    if(!xarVarFetch('categories_meta_description',    'array',  $categories_meta_description, NULL, XARVAR_DONT_SET)) {return;}
                    if(!xarVarFetch('categories_meta_keywords',    'array',  $categories_meta_keywords, NULL, XARVAR_DONT_SET)) {return;}
                    if(!xarVarFetch('categories_name',    'array',  $categories_name, NULL, XARVAR_DONT_SET)) {return;}

                    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                        $language_id = $languages[$i]['id'];
                        if (isset($categories_name[$language_id])) {
                            $q->addfield('categories_name',$categories_name[$language_id]);
                            if ($configuration['allow_category_descriptions'] == true) {
                                $q->addfield('categories_heading_title',$categories_heading_title[$language_id]);
                                $q->addfield('categories_description',$categories_description[$language_id]);
                                $q->addfield('categories_meta_title',$categories_meta_title[$language_id]);
                                $q->addfield('categories_meta_description',$categories_meta_description[$language_id]);
                                $q->addfield('categories_meta_keywords',$categories_meta_keywords[$language_id]);
                            }
                        }

                        $q1 = new xenQuery('SELECT');
                        if ($action == 'insert_category') {
                            $q1->addfield('categories_id',$categories_id);
                            $q1->addfield('language_id',$language_id);
                        }
                        elseif ($action == 'update_category') {
                            $q1->eq('categories_id',$categories_id);
                            $q1->eq('language_id',$language_id);
                        }
                            $q1->addtable('commerce_categories_description');
                            $q1->run();
                    }

                    if ($categories_image = new upload('categories_image', DIR_FS_CATALOG_IMAGES)) {
                        $q = new xenQuery('SELECT','commerce_categories');
                        $q->addfield('categories_image',$categories_image->filename);
                        $q->eq('categories_id',$categories_id);
                        $q->run();
                    }

                }
                xarResponseRedirect(xarModURL('commerce','admin','categories', array('cPath' => $cPath, 'cID' => $categories_id)));
            }
            break;
    }

    $configuration = xarModAPIFunc('commerce','admin','load_configuration');
    $data['configuration'] = $configuration;
    return $data;
}
?>