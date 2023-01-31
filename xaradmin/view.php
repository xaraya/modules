<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * view items
 */
function publications_admin_view($args=array())
{
    if (!xarSecurity::check('EditPublications')) return;

    // Get parameters
    if(!xarVar::fetch('startnum', 'isset', $startnum, 1,    xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('ptid',     'isset', $ptid,     NULL, xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('state',   'isset', $state,   NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('itemtype', 'isset', $itemtype, NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('catid',    'isset', $catid,    NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('sort', 'strlist:,:pre', $sort, NULL, xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('owner', 'isset', $owner, NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('lang',     'isset', $lang,     NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('pubdate',  'str:1', $pubdate,  NULL, xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('object',   'str:1', $object,  NULL, xarVar::NOT_REQUIRED)) {return;}

    extract($args);

    if (NULL === $ptid) {
        $ptid = xarSession::getVar('publications_current_pubtype');
        if (empty($ptid)) $ptid = xarModVars::get('publications', 'defaultpubtype');
    }
    xarSession::setVar('publications_current_pubtype', $ptid);

    $pubtypes = xarMod::apiFunc('publications','user','get_pubtypes');

    // Default parameters
    if (!isset($ptid)) {
        if (!empty($itemtype) && is_numeric($itemtype)) {
            // when we use some categories filter
            $ptid = $itemtype;
        } else {
            // we default to this for convenience
            $default = xarModVars::get('publications','defaultpubtype');
            if (!empty($default) && !xarSecurity::check('EditPublications',0,'Publication',"$default:All:All:All")) {
                // try to find some alternate starting pubtype if necessary
                foreach ($pubtypes as $id => $pubtype) {
                    if (xarSecurity::check('EditPublications',0,'Publication',"$id:All:All:All")) {
                        $ptid = $id;
                        break;
                    }
                }
            } else {
                $ptid = $default;
            }
        }
    }
    if (empty($ptid)) {
        $ptid = null;
    }
    if (empty($sort)) {
        $sort = 'date';
    }
    $data = array();
    $data['ptid'] = $ptid;
    $data['sort'] = $sort;
    $data['owner'] = $owner;
    $data['locale'] = $lang;
    $data['pubdate'] = $pubdate;

    if (!empty($catid)) {
        if (strpos($catid,' ')) {
            $cids = explode(' ',$catid);
            $andcids = true;
        } elseif (strpos($catid,'+')) {
            $cids = explode('+',$catid);
            $andcids = true;
        } else {
            $cids = explode('-',$catid);
            $andcids = false;
        }
    } else {
        $cids = array();
        $andcids = false;
    }
    $data['catid'] = $catid;

    if (empty($ptid)) {
        if (!xarSecurity::check('EditPublications',1,'Publication',"All:All:All:All")) return;
    } elseif (!is_numeric($ptid) || !isset($pubtypes[$ptid])) {
        return xarResponse::NotFound();
    } elseif (!xarSecurity::check('EditPublications',1,'Publication',"$ptid:All:All:All")) return;

    if (!empty($ptid)) {
        $settings = unserialize(xarModVars::get('publications', 'settings.'.$ptid));
    } else {
        $string = xarModVars::get('publications', 'settings');
        if (!empty($string)) {
            $settings = unserialize($string);
        }
    }
    if (isset($settings['admin_items_per_page'])) {
        $numitems = $settings['admin_items_per_page'];
    } else {
        $numitems = 30;
    }

    /*
    // Get item information
    $publications = xarMod::apiFunc('publications',
                             'user',
                             'getall',
                             array('startnum' => $startnum,
                                   'numitems' => $numitems,
                                   'ptid'     => $ptid,
                                   'owner' => $owner,
                                   'locale' => $lang,
                                   'pubdate'  => $pubdate,
                                   'cids'     => $cids,
                                   'sort'     => $sort,
                                   'andcids'  => $andcids,
                                   'extra'  => array('cids'),
                                   'state'   => $state));
*/
    // Save the current admin view, so that we can return to it after update
    $lastview = array('ptid' => $ptid,
                      'owner' => $owner,
                      'locale' => $lang,
                      'catid' => $catid,
                      'state' => $state,
                      'pubdate' => $pubdate,
                      'startnum' => $startnum > 1 ? $startnum : null);
    xarSession::setVar('Publications.LastView',serialize($lastview));

    $labels = array();
    $data['labels'] = $labels;

    // only show the date if this publication type has one
    $showdate = !empty($labels['pubdate']);
    $data['showdate'] = $showdate;
    // only show the state if this publication type has one
    $showstate = !empty($labels['state']);
                  // and if we're not selecting on it already
                  //&& (!is_array($state) || !isset($state[0]));
    $data['showstate'] = $showstate;

    $data['states'] = xarMod::apiFunc('publications','user','getstates');

    $items = array();
    /*
    if ($publications != false) {
        foreach ($publications as $article) {

            $item = array();

// TODO: adapt according to pubtype configuration
            // Title and pubdate
            $item['title'] = $article['title'];
            $item['summary'] = $article['summary'];
            $item['id'] = $article['id'];
            if (!empty($article['cids'])) {
                 $item['cids'] = $article['cids'];
            } else {
                 $item['cids'] = array();
            }

            if ($showdate) {
                $item['pubdate'] = $article['pubdate']; //strftime('%x %X %z', $article['pubdate']);
            }
            if ($showstate) {
                $item['state'] = $data['states'][$article['state']];
                // pre-select all submitted items
                if ($article['state'] == 0) {
                    $item['selected'] = 'checked';
                } else {
                    $item['selected'] = '';
                }
            }

            // Security check
            $input = array();
            $input['article'] = $article;
            $input['mask'] = 'ManagePublications';
            if (xarMod::apiFunc('publications','user','checksecurity',$input)) {
                $item['deleteurl'] = xarController::URL('publications',
                                              'admin',
                                              'delete',
                                              array('id' => $article['id']));
                $item['editurl'] = xarController::URL('publications',
                                            'admin',
                                            'modify',
                                            array('id' => $article['id']));
                $item['viewurl'] = xarController::URL('publications',
                                            'user',
                                            'display',
                                            array('id' => $article['id'],
                                                  'ptid' => $article['pubtype_id']));
            } else {
                $item['deleteurl'] = '';

                $input['mask'] = 'EditPublications';
                if (xarMod::apiFunc('publications','user','checksecurity',$input)) {
                    $item['editurl'] = xarController::URL('publications',
                                                'admin',
                                                'modify',
                                                array('id' => $article['id']));
                    $item['viewurl'] = xarController::URL('publications',
                                                'user',
                                                'display',
                                                array('id' => $article['id'],
                                                      'ptid' => $article['pubtype_id']));
                } else {
                    $item['editurl'] = '';

                    $input['mask'] = 'ReadPublications';
                    if (xarMod::apiFunc('publications','user','checksecurity',$input)) {
                        $item['viewurl'] = xarController::URL('publications',
                                                    'user',
                                                    'display',
                                                    array('id' => $article['id'],
                                                          'ptid' => $article['pubtype_id']));
                    } else {
                        $item['viewurl'] = '';
                    }
                }
            }

            $item['deletetitle'] = xarML('Delete');
            $item['viewtitle'] = xarML('View');

            $items[] = $item;
        }
    }
    */
    $data['items'] = $items;

/*
    // Add pager
    $data['pager'] = xarTplGetPager($startnum,
                            xarMod::apiFunc('publications', 'user', 'countitems',
                                          array('ptid' => $ptid,
                                                'owner' => $owner,
                                                'locale' => $lang,
                                                'pubdate' => $pubdate,
                                                'cids' => $cids,
                                                'andcids' => $andcids,
                                                'state' => $state)),
                            xarController::URL('publications', 'admin', 'view',
                                      array('startnum' => '%%',
                                            'ptid' => $ptid,
                                            'owner' => $owner,
                                            'locale' => $lang,
                                            'pubdate' => $pubdate,
                                            'catid' => $catid,
                                            'state' => $state)),
                            $numitems);

    // Create filters based on publication type
    */
    $pubfilters = array();
    /*
    foreach ($pubtypes as $id => $pubtype) {
        if (!xarSecurity::check('EditPublications',0,'Publication',"$id:All:All:All")) {
            continue;
        }
        $pubitem = array();
        if ($id == $ptid) {
            $pubitem['plink'] = '';
        } else {
            $pubitem['plink'] = xarController::URL('publications','admin','view',
                                         array('ptid' => $id));
        }
        $pubitem['ptitle'] = $pubtype['description'];
        $pubfilters[] = $pubitem;
    }
*/
    $data['pubfilters'] = $pubfilters;
    // Create filters based on article state
    $statefilters = array();
    if (!empty($labels['state'])) {
        $statefilters[] = array('stitle' => xarML('All'),
                                 'slink' => !is_array($state) ? '' :
                                                xarController::URL('publications','admin','view',
                                                          array('ptid' => $ptid,
                                                                'catid' => $catid)));
        foreach ($data['states'] as $id => $name) {
            $statefilters[] = array('stitle' => $name,
                                     'slink' => (is_array($state) && $state[0] == $id) ? '' :
                                                    xarController::URL('publications','admin','view',
                                                              array('ptid' => $ptid,
                                                                    'catid' => $catid,
                                                                    'state' => array($id))));
        }
    }
    $data['statefilters'] = $statefilters;
    $data['changestatelabel'] = xarML('Change Status');
    // Add link to create new article
    if (xarSecurity::check('SubmitPublications',0,'Publication',"$ptid:All:All:All")) {
        $newurl = xarController::URL('publications',
                           'admin',
                           'new',
                           array('ptid' => $ptid));
        $data['shownewlink'] = true;
    } else {
        $newurl = '';
        $data['shownewlink'] = false;
    }
    $data['newurl'] = $newurl;
// TODO: Hook category block someday ?
    xarCoreCache::setCached('Blocks.categories','module','publications');
    xarCoreCache::setCached('Blocks.categories','type','admin');
    xarCoreCache::setCached('Blocks.categories','func','view');
    xarCoreCache::setCached('Blocks.categories','itemtype',$ptid);
    if (!empty($ptid) && !empty($pubtypes[$ptid]['description'])) {
        xarCoreCache::setCached('Blocks.categories','title',$pubtypes[$ptid]['description']);
    }
    xarCoreCache::setCached('Blocks.categories','cids',$cids);

    if (!empty($ptid)) {
        $template = $pubtypes[$ptid]['name'];
    } else {
// TODO: allow templates per category ?
       $template = null;
    }
    
    // Get the available publications objects
    $object = DataObjectMaster::getObjectList(array('objectid' => 1));
    $items = $object->getItems();
    $options = array();
    foreach ($items as $item)
        if (strpos($item['name'],'publications_') !== false)
            $options[] = array('id' => $item['objectid'], 'name' => $item['name'], 'title' => $item['label']);
    $data['objects'] = $options;

    // Only show top level documents, not translations
    sys::import('xaraya.structures.query');
    $q = new Query();
    $q->eq('parent_id',0);
    $q->eq('pubtype_id',$ptid);
    
    // Suppress deleted items if not an admin
    // Remove this once listing property works with dataobject access
    if (!xarRoles::isParent('Administrators',xarUser::getVar('uname'))) $q->ne('state',0);
    $data['conditions'] = $q;

    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $ptid));
    $data['object'] = DataObjectMaster::getObjectList(array('name' => $pubtypeobject->properties['name']->value));

    // Flag this as the current list view
    xarSession::setVar('publications_current_listview', xarServer::getCurrentURL(array('ptid' => $ptid)));
    
    return xarTplModule('publications', 'admin', 'view', $data, $template);
}

?>