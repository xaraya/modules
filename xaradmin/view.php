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
 * view items
 */
function publications_admin_view($args)
{
    // Get parameters
    if(!xarVarFetch('startnum', 'isset', $startnum, 1,    XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('ptid',     'isset', $ptid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('state',   'isset', $state,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('catid',    'isset', $catid,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sort', 'strlist:,:pre', $sort, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('owner', 'isset', $owner, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('lang',     'isset', $lang,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('pubdate',  'str:1', $pubdate,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('object',   'str:1', $object,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    extract($args);

    $pubtypes = xarModAPIFunc('publications','user','getpubtypes');

    // Default parameters
    if (!isset($ptid)) {
        if (!empty($itemtype) && is_numeric($itemtype)) {
            // when we use some categories filter
            $ptid = $itemtype;
        } else {
            // we default to this for convenience
            $default = xarModVars::get('publications','defaultpubtype');
            if (!empty($default) && !xarSecurityCheck('EditPublications',0,'Publication',"$default:All:All:All")) {
                // try to find some alternate starting pubtype if necessary
                foreach ($pubtypes as $id => $pubtype) {
                    if (xarSecurityCheck('EditPublications',0,'Publication',"$id:All:All:All")) {
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
        if (!xarSecurityCheck('EditPublications',0,'Publication',"All:All:All:All")) {
            $msg = xarML('You have no permission to edit #(1)',
                         'Publications');
        throw new ForbiddenOperationException(null, $msg);
        }
    } elseif (!is_numeric($ptid) || !isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid publication type');
        throw new BadParameterException(null,$msg);
    } elseif (!xarSecurityCheck('EditPublications',0,'Publication',"$ptid:All:All:All")) {
        $msg = xarML('You have no permission to edit #(1)',
                     $pubtypes[$ptid]['description']);
        throw new ForbiddenOperationException(null, $msg);
    }

    if (!empty($ptid)) {
        $settings = unserialize(xarModVars::get('publications', 'settings.'.$ptid));
    } else {
        $string = xarModVars::get('publications', 'settings');
        if (!empty($string)) {
            $settings = unserialize($string);
        }
    }
    if (isset($settings['adminitemsperpage'])) {
        $numitems = $settings['adminitemsperpage'];
    } else {
        $numitems = 30;
    }

    /*
    // Get item information
    $publications = xarModAPIFunc('publications',
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
/*
    if (!empty($ptid)) {
        foreach ($pubtypes[$ptid]['config'] as $field => $value) {
            $labels[$field] = $value['label'];
        }
    } else {
        $pubfields = xarModAPIFunc('publications','user','getpubfields');
        foreach ($pubfields as $field => $value) {
            $labels[$field] = $value['label'];
        }
    }
    */
    $data['labels'] = $labels;

    // only show the date if this publication type has one
    $showdate = !empty($labels['pubdate']);
    $data['showdate'] = $showdate;
    // only show the state if this publication type has one
    $showstate = !empty($labels['state']);
                  // and if we're not selecting on it already
                  //&& (!is_array($state) || !isset($state[0]));
    $data['showstate'] = $showstate;

    $data['states'] = xarModAPIFunc('publications','user','getstates');

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
            if (xarModAPIFunc('publications','user','checksecurity',$input)) {
                $item['deleteurl'] = xarModURL('publications',
                                              'admin',
                                              'delete',
                                              array('id' => $article['id']));
                $item['editurl'] = xarModURL('publications',
                                            'admin',
                                            'modify',
                                            array('id' => $article['id']));
                $item['viewurl'] = xarModURL('publications',
                                            'user',
                                            'display',
                                            array('id' => $article['id'],
                                                  'ptid' => $article['pubtype_id']));
            } else {
                $item['deleteurl'] = '';

                $input['mask'] = 'EditPublications';
                if (xarModAPIFunc('publications','user','checksecurity',$input)) {
                    $item['editurl'] = xarModURL('publications',
                                                'admin',
                                                'modify',
                                                array('id' => $article['id']));
                    $item['viewurl'] = xarModURL('publications',
                                                'user',
                                                'display',
                                                array('id' => $article['id'],
                                                      'ptid' => $article['pubtype_id']));
                } else {
                    $item['editurl'] = '';

                    $input['mask'] = 'ReadPublications';
                    if (xarModAPIFunc('publications','user','checksecurity',$input)) {
                        $item['viewurl'] = xarModURL('publications',
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
            $item['edittitle'] = xarML('Edit');
            $item['viewtitle'] = xarML('View');

            $items[] = $item;
        }
    }
    */
    $data['items'] = $items;

/*
    // Add pager
    $data['pager'] = xarTplGetPager($startnum,
                            xarModAPIFunc('publications', 'user', 'countitems',
                                          array('ptid' => $ptid,
                                                'owner' => $owner,
                                                'locale' => $lang,
                                                'pubdate' => $pubdate,
                                                'cids' => $cids,
                                                'andcids' => $andcids,
                                                'state' => $state)),
                            xarModURL('publications', 'admin', 'view',
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
        if (!xarSecurityCheck('EditPublications',0,'Publication',"$id:All:All:All")) {
            continue;
        }
        $pubitem = array();
        if ($id == $ptid) {
            $pubitem['plink'] = '';
        } else {
            $pubitem['plink'] = xarModURL('publications','admin','view',
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
                                                xarModURL('publications','admin','view',
                                                          array('ptid' => $ptid,
                                                                'catid' => $catid)));
        foreach ($data['states'] as $id => $name) {
            $statefilters[] = array('stitle' => $name,
                                     'slink' => (is_array($state) && $state[0] == $id) ? '' :
                                                    xarModURL('publications','admin','view',
                                                              array('ptid' => $ptid,
                                                                    'catid' => $catid,
                                                                    'state' => array($id))));
        }
    }
    $data['statefilters'] = $statefilters;
    $data['changestatelabel'] = xarML('Change Status');
    // Add link to create new article
    if (xarSecurityCheck('SubmitPublications',0,'Publication',"$ptid:All:All:All")) {
        $newurl = xarModURL('publications',
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
    xarVarSetCached('Blocks.categories','module','publications');
    xarVarSetCached('Blocks.categories','type','admin');
    xarVarSetCached('Blocks.categories','func','view');
    xarVarSetCached('Blocks.categories','itemtype',$ptid);
    if (!empty($ptid) && !empty($pubtypes[$ptid]['description'])) {
        xarVarSetCached('Blocks.categories','title',$pubtypes[$ptid]['description']);
    }
    xarVarSetCached('Blocks.categories','cids',$cids);

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

    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $ptid));
    $data['object'] = DataObjectMaster::getObjectList(array('name' => $pubtypeobject->properties['name']->value));
    return xarTplModule('publications', 'admin', 'view', $data, $template);
}

?>