<?php
/**
 * User management view of articles
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * View articles for an user/moderator. This function shows a page from which articles can be managed
 *
 * @param int startnum Defaults to 1
 * @param int ptid OPTIONAL
 * @param status OPTIONAL
 * @param int itemtype OPTIONAL
 * @param catid OPTIONAL
 * @param int authorid OPTIONAL
 * @param lang OPTIONAL
 * @param pubdate OPTIONAL
 * @return mixed. Calls the template function to show the article listing.
 */
function articles_user_viewlist($args)
{
    // Get parameters
    if(!xarVarFetch('startnum', 'isset', $startnum, 1,    XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('ptid',     'isset', $ptid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('status',   'isset', $status,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('catid',    'isset', $catid,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('authorid', 'isset', $authorid, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('lang',     'isset', $lang,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('pubdate',  'str:1', $pubdate,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('sort',     'enum:title:pubdate:status',  $sort,     'pubdate', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('sortdir',  'isset', $sortdir,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    extract($args);

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    // Default parameters
    if (!isset($ptid)) {
        if (!empty($itemtype) && is_numeric($itemtype)) {
            // when we use some categories filter
            $ptid = $itemtype;
        } else {
            // we default to this for convenience
            $default = xarModGetVar('articles','defaultpubtype');
            if (!empty($default) && !xarSecurityCheck('EditArticles',0,'Article',"$default:All:All:All")) {
                // try to find some alternate starting pubtype if necessary
                foreach ($pubtypes as $id => $pubtype) {
                    if (xarSecurityCheck('EditArticles',0,'Article',"$id:All:All:All")) {
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
    $data = array();
    $data['ptid'] = $ptid;
    $data['authorid'] = $authorid;
    $data['language'] = $lang;
    $data['pubdate'] = $pubdate;

    $authid = xarSecGenAuthKey();

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
        if (!xarSecurityCheck('EditArticles',0,'Article',"All:All:All:All")) {
            $msg = xarML('You have no permission to edit #(1)',
                         'Articles');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                            new SystemException($msg));
            return;
        }
    } elseif (!is_numeric($ptid) || !isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid publication type');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return;
    } elseif (!xarSecurityCheck('EditArticles',0,'Article',"$ptid:All:All:All")) {
        $msg = xarML('You have no permission to edit #(1)',
                     $pubtypes[$ptid]['descr']);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                        new SystemException($msg));
        return;
    }

    if (!empty($ptid)) {
        $settings = unserialize(xarModGetVar('articles', 'settings.'.$ptid));
    } else {
        $string = xarModGetVar('articles', 'settings');
        if (!empty($string)) {
            $settings = unserialize($string);
        }
    }
    if (isset($settings['adminitemsperpage'])) {
        $numitems = $settings['adminitemsperpage'];
    } else {
        $numitems = 30;
    }

    $labels = array();
    if (!empty($ptid)) {
        foreach ($pubtypes[$ptid]['config'] as $field => $value) {
            $labels[$field] = $value['label'];
        }
    } else {
        $pubfields = xarModAPIFunc('articles','user','getpubfields');
        foreach ($pubfields as $field => $value) {
            $labels[$field] = $value['label'];
        }
    }
    $data['labels'] = $labels;

    // only show the date if this publication type has one
    $showdate = !empty($labels['pubdate']);
    $data['showdate'] = $showdate;
    // only show the status if this publication type has one
    $showstatus = !empty($labels['status']);
    // and if we're not selecting on it already
    //&& (!is_array($status) || !isset($status[0]));
    $data['showstatus'] = $showstatus;

    // fall back sort field accordingly
    if (!$showdate && $sort == 'pubdate') {
        $sort = 'title';
    }
    if (!$showstatus && $sort == 'status') {
        $sort = 'title';
    }

    if($sortdir == NULL || trim(strtolower($sortdir)) == 'asc') {
        $sortdir = " ASC";
    } else {
        $sortdir = " DESC";
    }

    $data['sortdir'] = trim(strtolower($sortdir));
    $data['othersortdir'] = $data['sortdir'] == 'asc' ? 'desc' : 'asc';
    $data['sort'] = $sort;

    $data['states'] = xarModAPIFunc('articles','user','getstates');

    // Get item information
    $articles = xarModAPIFunc('articles',
                             'user',
                             'getall',
                             array('startnum' => $startnum,
                                   'numitems' => $numitems,
                                   'ptid'     => $ptid,
                                   'authorid' => $authorid,
                                   'language' => $lang,
                                   'pubdate'  => $pubdate,
                                   'cids'     => $cids,
                                   'andcids'  => $andcids,
                                   'sort'     => $sort.$sortdir,
                                   'status'   => $status));

    // Save the current admin view, so that we can return to it after update
    $lastview = array('ptid' => $ptid,
                      'authorid' => $authorid,
                      'language' => $lang,
                      'catid' => $catid,
                      'status' => $status,
                      'pubdate' => $pubdate,
                      'startnum' => $startnum > 1 ? $startnum : null,
                      'sort' => $sort,
                      'sortdir' => $sortdir);
    xarSessionSetVar('Articles.LastView',serialize($lastview));

    $items = array();
    if ($articles != false) {
        foreach ($articles as $article) {

            $item = array();

// TODO: adapt according to pubtype configuration
            // Title and pubdate
            $item['title'] = $article['title'];
            $item['aid'] = $article['aid'];

            if ($showdate) {
                $item['pubdate'] = $article['pubdate']; //strftime('%x %X %z', $article['pubdate']);
            }
            if ($showstatus) {
                $item['status'] = $data['states'][$article['status']];
                $item['statusnumeric'] = $article['status'];
                
                // pre-select all submitted items
                if ($article['status'] == 0) {
                    $item['selected'] = true;
                } else {
                    $item['selected'] = false;
                }
            }

            // Security check
            $input = array();
            $input['article'] = $article;
            $input['mask'] = 'DeleteArticles';
            if (xarModAPIFunc('articles','user','checksecurity',$input)) {
                $item['deleteurl'] = xarModURL('articles',
                                              'user',
                                              'delete',
                                              array('aid' => $article['aid'], 'authid' => $authid));
                $item['editurl'] = xarModURL('articles',
                                            'user',
                                            'modify',
                                            array('aid' => $article['aid']));
                $item['viewurl'] = xarModURL('articles',
                                            'user',
                                            'display',
                                            array('aid' => $article['aid'],
                                                  'ptid' => $article['pubtypeid']));
            } else {
                $item['deleteurl'] = '';

                $input['mask'] = 'EditArticles';
                if (xarModAPIFunc('articles','user','checksecurity',$input)) {
                    $item['editurl'] = xarModURL('articles',
                                                'user',
                                                'modify',
                                                array('aid' => $article['aid']));
                    $item['viewurl'] = xarModURL('articles',
                                                'user',
                                                'display',
                                                array('aid' => $article['aid'],
                                                      'ptid' => $article['pubtypeid']));
                } else {
                    $item['editurl'] = '';

                    $input['mask'] = 'ReadArticles';
                    if (xarModAPIFunc('articles','user','checksecurity',$input)) {
                        $item['viewurl'] = xarModURL('articles',
                                                    'user',
                                                    'display',
                                                    array('aid' => $article['aid'],
                                                          'ptid' => $article['pubtypeid']));
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
    $data['items'] = $items;
    $data['sortinfo'] = array(
        'ptid' => $ptid,
        'authorid' => $authorid,
        'language' => $lang,
        'catid' => $catid,
        'cids'     => $cids,
        'andcids'  => $andcids,
        'status'   => $status);

    // Add pager
    $data['pager'] = xarTplGetPager($startnum,
                            xarModAPIFunc('articles', 'user', 'countitems',
                                          array('ptid' => $ptid,
                                                'authorid' => $authorid,
                                                'language' => $lang,
                                                'pubdate' => $pubdate,
                                                'cids' => $cids,
                                                'andcids' => $andcids,
                                                'status' => $status)),
                            xarModURL('articles', 'user', 'viewlist',
                                      array('startnum' => '%%',
                                            'ptid' => $ptid,
                                            'authorid' => $authorid,
                                            'language' => $lang,
                                            'pubdate' => $pubdate,
                                            'catid' => $catid,
                                            'status' => $status)),
                            $numitems);

    // Create filters based on publication type
    $pubfilters = array();
    foreach ($pubtypes as $id => $pubtype) {
        if (!xarSecurityCheck('EditArticles',0,'Article',"$id:All:All:All")) {
            continue;
        }
        $pubitem = array();
        if ($id == $ptid) {
            $pubitem['plink'] = '';
        } else {
            $pubitem['plink'] = xarModURL('articles','user','viewlist',
                                         array('ptid' => $id));
        }
        $pubitem['ptitle'] = $pubtype['descr'];
        $pubfilters[] = $pubitem;
    }
    $data['pubfilters'] = $pubfilters;
    // Create filters based on article status
    $statusfilters = array();
    if (!empty($labels['status'])) {
        $statusfilters[] = array('stitle' => xarML('All'),
                                 'slink' => !is_array($status) ? '' :
                                                xarModURL('articles','user','viewlist',
                                                          array('ptid' => $ptid,
                                                                'catid' => $catid)));
        foreach ($data['states'] as $id => $name) {
            $statusfilters[] = array('stitle' => $name,
                                     'slink' => (is_array($status) && $status[0] == $id) ? '' :
                                                    xarModURL('articles','user','viewlist',
                                                              array('ptid' => $ptid,
                                                                    'catid' => $catid,
                                                                    'status' => array($id))));
        }
    }
    $data['statusfilters'] = $statusfilters;
    $data['changestatuslabel'] = xarML('Change Status');
    // Add link to create new article
    if (xarSecurityCheck('SubmitArticles',0,'Article',"$ptid:All:All:All")) {
        $newurl = xarModURL('articles',
                           'user',
                           'new',
                           array('ptid' => $ptid));
        $data['shownewlink'] = true;
    } else {
        $newurl = '';
        $data['shownewlink'] = false;
    }
    $data['newurl'] = $newurl;
// TODO: Hook category block someday ?
    xarVarSetCached('Blocks.categories','module','articles');
    xarVarSetCached('Blocks.categories','type','admin');
    xarVarSetCached('Blocks.categories','func','view');
    xarVarSetCached('Blocks.categories','itemtype',$ptid);
    if (!empty($ptid) && !empty($pubtypes[$ptid]['descr'])) {
        xarVarSetCached('Blocks.categories','title',$pubtypes[$ptid]['descr']);
    }
    xarVarSetCached('Blocks.categories','cids',$cids);

    if (!empty($ptid)) {
        $template = $pubtypes[$ptid]['name'];
        xarTplSetPageTitle(xarML('View #(1)', $pubtypes[$ptid]['descr']));
    } else {
// TODO: allow templates per category ?
       $template = null;
       xarTplSetPageTitle(xarML('View'));
    }

    return xarTplModule('articles', 'user', 'viewlist', $data, $template);
}

?>