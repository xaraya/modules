<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file: Categories Navigation Block
// ----------------------------------------------------------------------

/**
 * initialise block
 */
function categories_navigationblock_init()
{
    return true;
}

/**
 * get information on block
 */
function categories_navigationblock_info()
{
    // Values
    return array('text_type' => 'Navigation',
                 'module' => 'categories',
                 'text_type_long' => 'Show navigation',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}

/**
 * display block
 */
function categories_navigationblock_display($blockinfo)
{
    // Security Check
    if(!xarSecurityCheck('ViewBaseBlocks',0,'Block',"All:$blockinfo[title]:All")) return;

// TODO: for multi-module pages, we'll need some other reference point(s)
//       (e.g. cross-module categories defined in categories admin ?)
    // Get current module
    if (xarVarIsCached('Blocks.categories','module')) {
       $modname = xarVarGetCached('Blocks.categories','module');
    }
    if (empty($modname)) {
        $modname = xarModGetName();
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        return;
    }

    // Get current item type (if any)
    if (xarVarIsCached('Blocks.categories','itemtype')) {
        $itemtype = xarVarGetCached('Blocks.categories','itemtype');
    }
    if (empty($itemtype)) {
        $itemtype = null;
    }

    // Get current item id (if any)
    if (xarVarIsCached('Blocks.categories','itemid')) {
        $itemid = xarVarGetCached('Blocks.categories','itemid');
    }
    if (empty($itemid)) {
        $itemid = null;
    }

    // get number of categories from current settings
    if (!empty($itemtype)) {
        $numcats = (int) xarModGetVar($modname, 'number_of_categories.'.$itemtype);
    } else {
        $numcats = (int) xarModGetVar($modname, 'number_of_categories');
    }
    if (empty($numcats) || !is_numeric($numcats)) {
        // no categories to show here -> return empty output
        return;
    }

    // try to get master cids from current settings
    if (!empty($itemtype)) {
        $cidlist = xarModGetVar($modname,'mastercids.'.$itemtype);
    } else {
        $cidlist = xarModGetVar($modname,'mastercids');
    }
    if (empty($cidlist)) {
        // no categories to show here -> return empty output
        return;
    } else {
        $mastercids = explode(';',$cidlist);
        sort($mastercids,SORT_NUMERIC);
    }

    // Load categories user API
    if (!xarModAPILoad('categories','user')) return;

// TODO: sanitize
    // Get current categories
    if (xarVarIsCached('Blocks.categories','catid')) {
       $catid = xarVarGetCached('Blocks.categories','catid');
    }
    if (empty($catid)) {
        // try to get catid from input
        $catid = xarVarCleanFromInput('catid');
    }
    // turn $catid into $cids array (and set $andcids flag)
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
        if (xarVarIsCached('Blocks.categories','cids')) {
            $cids = xarVarGetCached('Blocks.categories','cids');
        }
        if (xarVarIsCached('Blocks.categories','andcids')) {
            $andcids = xarVarGetCached('Blocks.categories','andcids');
        }
        if (empty($cids)) {
            // try to get cids from input
            if(!xarVarFetch('cids',    'isset', $cids,         NULL, XARVAR_DONT_SET)) {return;}
            if(!xarVarFetch('andcids', 'isset', $andcids, false, XARVAR_NOT_REQUIRED)) {return;}

            if (empty($cids)) {
                $cids = array();
                if (!empty($itemid)) {
                    $links = xarModAPIFunc('categories','user','getlinks',
                                          array('modid' => $modid,
                                                'iids' => array($itemid)));
                    if (!empty($links) && count($links) > 0) {
                        $cids = array_keys($links);
                    }
                }
            }
        }
    }

// TODO: allow override by module (via xarVarGetCached)
    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['layout'])) {
        $vars['layout'] = 1;
    }
    if (empty($vars['showcatcount'])) {
        $vars['showcatcount'] = 0;
    }
    if (empty($vars['showchildren'])) {
        $vars['showchildren'] = 0;
    }

    // Get current category counts (optional array of cid => count)
    if (empty($vars['showcatcount'])) {
        $catcount = array();
    } elseif (xarVarIsCached('Blocks.categories','catcount')) {
        $catcount = xarVarGetCached('Blocks.categories','catcount');
    } else {
        // get number of items per category (for this module)
        $catcount = xarModAPIFunc('categories','user','groupcount',
                                 array('modid' => $modid));
        xarVarSetCached('Blocks.categories','catcount',$catcount);
    }

// TODO: use hooks/widgets/... someday ?

    // Specify type=... & func = ... arguments for xarModURL()
    if (xarVarIsCached('Blocks.categories','type')) {
        $type = xarVarGetCached('Blocks.categories','type');
    }
    if (empty($type)) {
        $type = 'user';
    }
    if (xarVarIsCached('Blocks.categories','func')) {
        $func = xarVarGetCached('Blocks.categories','func');
    }
    if (empty($func)) {
        $func = 'view';
    }

    $blockinfo['content'] = '';
    $data = array();

    // Generate output
    switch ($vars['layout']) {

    case 3: // prev/next (bottom block)
        if (empty($cids) || count($cids) != 1 || in_array($cids[0], $mastercids)) {
            // nothing to show here
            return;
        } else {
            $template = 'nav-prevnext';
            // See if we need to show anything
            if (xarVarIsCached('Blocks.categories','showprevnext')) {
                $showprevnext = xarVarGetCached('Blocks.categories','showprevnext');
                if (empty($showprevnext)) {
                    return;
                }
            }
            $cat = xarModAPIFunc('categories','user','getcatinfo',
                                array('cid' => $cids[0]));
            if (empty($cat)) {
                return;
            }
            $neighbours = xarModAPIFunc('categories','user','getneighbours',
                                       $cat);
            if (empty($neighbours) || count($neighbours) == 0) {
                return;
            }
            foreach ($neighbours as $neighbour) {
//                if ($neighbour['link'] == 'parent') {
//                    $data['uplabel'] = $neighbour['name'];
//                    $data['uplink'] = xarModURL($modname,$type,$func,
//                                               array('itemtype' => $itemtype,
//                                                     'catid' => $neighbour['cid']));
//                } elseif ($neighbour['link'] == 'previous') {
                if ($neighbour['link'] == 'previous') {
                    $data['prevlabel'] = $neighbour['name'];
                    $data['prevlink'] = xarModURL($modname,$type,$func,
                                                 array('itemtype' => $itemtype,
                                                       'catid' => $neighbour['cid']));
                } elseif ($neighbour['link'] == 'next') {
                    $data['nextlabel'] = $neighbour['name'];
                    $data['nextlink'] = xarModURL($modname,$type,$func,
                                                 array('itemtype' => $itemtype,
                                                       'catid' => $neighbour['cid']));
                }
            }
            if (!isset($data['nextlabel']) &&
                !isset($data['prevlabel'])) {
                return;
            }
//            if (!isset($data['uplabel'])) {
//                $data['uplabel'] = '&nbsp;';
//            }
        }
        break;

    case 2: // crumbtrails (top block)
        if (empty($cids) || count($cids) == 0) {
            $template = 'nav-rootcats';
            $data['cattitle'] = xarML('Browse in');
            $data['catitems'] = array();

            // Get root categories
            $catlist = xarModAPIFunc('categories','user','getcatinfo',
                                    array('cids' => $mastercids));
            $join = '';
            foreach ($catlist as $cat) {
            // TODO: now this is a tricky part...
                $link = xarModURL($modname,$type,$func,
                                 array('catid' => $cat['cid'],
                                       'itemtype' => $itemtype));
                $label = xarVarPrepForDisplay($cat['name']);
                $data['catitems'][] = array('catlabel' => $label,
                                            'catlink' => $link,
                                            'catjoin' => $join);
                $join = ' | ';
            }
        } else {
            $template = 'nav-trails';
            if (!empty($andcids)) {
                $data['cattitle'] = xarML('Browse in');
            } else {
                $data['cattitle'] = xarML('Browse in');
            }
            $data['cattrails'] = array();

            $descriptions = array();
// TODO: stop at root categories
            foreach ($cids as $cid) {
                // Get category information
                $parents = xarModAPIFunc('categories','user','getparents',
                                        array('cid' => $cid));
                if (empty($parents)) {
                    continue;
                }
                $catitems = array();
                $curcount = 0;
            // TODO: now this is a tricky part...
                $label = xarML('All');
                $link = xarModURL($modname,$type,$func,
                                 array('itemtype' => $itemtype));
                $join = '';
                $catitems[] = array('catlabel' => $label,
                                    'catlink' => $link,
                                    'catjoin' => $join);
                $join = ' &gt; ';
                foreach ($parents as $cat) {
                    $label = xarVarPrepForDisplay($cat['name']);
                    if ($cat['cid'] == $cid && empty($itemid) && empty($andcids)) {
                        $link = '';
                    } else {
                    // TODO: now this is a tricky part...
                        $link = xarModURL($modname,$type,$func,
                                         array('catid' => $cat['cid'],
                                               'itemtype' => $itemtype));
                    }
                    if ($cat['cid'] == $cid) {
                        // show optional count
                        if (isset($catcount[$cat['cid']])) {
                            $curcount = $catcount[$cat['cid']];
                        }
                        if (!empty($cat['description'])) {
                            $descriptions[] = xarVarPrepHTMLDisplay($cat['description']);
                        } else {
                            $descriptions[] = xarVarPrepForDisplay($cat['name']);
                        }
                        // save current category info for icon etc.
                        if (count($cids) == 1) {
                            $curcat = $cat;
                        }
                    }
                    $catitems[] = array('catlabel' => $label,
                                        'catlink' => $link,
                                        'catjoin' => $join);
                }
                $data['cattrails'][] = array('catitems' => $catitems,
                                             'catcount' => $curcount);
            }

            // Add filters to select on all categories or any categories
            if (count($cids) > 1) {
                $catitems = array();
                if (!empty($itemid) || !empty($andcids)) {
                    $label = xarML('Any of these categories');
                    $link = xarModURL($modname,$type,$func,
                                      array('catid' => join('-',$cids),
                                            'itemtype' => $itemtype));
                    $join = '';
                    $catitems[] = array('catlabel' => $label,
                                        'catlink' => $link,
                                        'catjoin' => $join);
                }
                if (empty($andcids)) {
                    $label = xarML('All of these categories');
                    $link = xarModURL($modname,$type,$func,
                                      array('catid' => join('+',$cids),
                                            'itemtype' => $itemtype));
                    if (!empty($itemid)) {
                        $join = '-';
                    } else {
                        $join = '';
                    }
                    $catitems[] = array('catlabel' => $label,
                                        'catlink' => $link,
                                        'catjoin' => $join);
                }
                $curcount = 0;
                $data['cattrails'][] = array('catitems' => $catitems,
                                             'catcount' => $curcount);
            }

        // TODO: move off to nav-trails template ?
            // Build category description
            if (!empty($itemid)) {
                $data['catdescr'] = join(' + ', $descriptions);
            } elseif (!empty($andcids)) {
                $data['catdescr'] = join(' ' . xarML('and') . ' ', $descriptions);
            } else {
                $data['catdescr'] = join(' ' . xarML('or') . ' ', $descriptions);
            }

            if (count($cids) != 1) {
                break;
            }
            // set the page title to the current module + category if no item is displayed
            if (empty($itemid)) {
                // Get current title
                if (xarVarIsCached('Blocks.categories','title')) {
                    $title = xarVarGetCached('Blocks.categories','title');
                }
                if (empty($title)) {
                    $modinfo = xarModGetInfo($modid);
                    $title = $modinfo['displayname'];
                }
                $title = xarVarPrepForDisplay($title);
                $title .= ' :: ' . xarVarPrepForDisplay($curcat['name']);
                xarTplSetPageTitle($title);
            }

        // TODO: don't show icons when displaying items ?
            if (!empty($curcat['image'])) {
                // find the image in categories (we need to specify the module here)
                $data['catimage'] = xarTplGetImage($curcat['image'],'categories');
                $data['catname'] = xarVarPrepForDisplay($curcat['name']);
            }
            if ($vars['showchildren'] == 2) {
                // Load categories user API
                if (!xarModAPILoad('categories','visual')) return;

                // Get child categories (all sub-levels)
                $childlist = xarModAPIFunc('categories','visual','listarray',
                                          array('cid' => $cids[0]));
                if (empty($childlist) || count($childlist) == 0) {
                    break;
                }
                foreach ($childlist as $info) {
                    if ($info['id'] == $cids[0]) {
                        continue;
                    }
                    $label = xarVarPrepForDisplay($info['name']);
                // TODO: now this is a tricky part...
                    $link = xarModURL($modname,$type,$func,
                                     array('catid' => $info['id'],
                                           'itemtype' => $itemtype));
                    if (!empty($catcount[$info['id']])) {
                        $count = $catcount[$info['id']];
                    } else {
                        $count = 0;
                    }
/* don't show descriptions in (potentially) multi-level trees
                    if (!empty($info['description'])) {
                        $descr = xarVarPrepHTMLDisplay($info['description']);
                    } else {
                        $descr = '';
                    }
*/
                    $data['catlines'][] = array('catlabel' => $label,
                                                'catlink' => $link,
                                              //  'catdescr' => $descr,
                                                'catcount' => $count,
                                                'beforetags' => $info['beforetags'],
                                                'aftertags' => $info['aftertags']);

                }
                unset($childlist);
            } elseif ($vars['showchildren'] == 1) {
                // Get child categories (1 level only)
                $children = xarModAPIFunc('categories','user','getchildren',
                                         array('cid' => $cids[0]));
                if (empty($children) || count($children) == 0) {
                    break;
                }
                $data['catlines'] = array();
            // TODO: don't show icons when displaying items ?
                $data['caticons'] = array();
                $numicons = 0;
                foreach ($children as $cat) {
                // TODO: now this is a tricky part...
                    $label = xarVarPrepForDisplay($cat['name']);
                    $link = xarModURL($modname,$type,$func,
                                     array('catid' => $cat['cid'],
                                           'itemtype' => $itemtype));
                    if (!empty($catcount[$cat['cid']])) {
                        $count = $catcount[$cat['cid']];
                    } else {
                        $count = 0;
                    }
                    if (!empty($cat['image'])) {
                        // find the image in categories (we need to specify the module here)
                        $image = xarTplGetImage($cat['image'],'categories');
                        $numicons++;
                        $data['caticons'][] = array('catlabel' => $label,
                                                    'catlink' => $link,
                                                    'catimage' => $image,
                                                    'catcount' => $count,
                                                    'catnum' => $numicons);
                    } else {
                        if (!empty($cat['description']) && $cat['description'] != $cat['name']) {
                            $descr = xarVarPrepHTMLDisplay($cat['description']);
                        } else {
                            $descr = '';
                        }
                        $beforetags = '<li>';
                        $aftertags = '</li>';
                        $data['catlines'][] = array('catlabel' => $label,
                                                    'catlink' => $link,
                                                    'catdescr' => $descr,
                                                    'catcount' => $count,
                                                    'beforetags' => $beforetags,
                                                    'aftertags' => $aftertags);
                    }
                }
                unset($children);
                if (count($data['catlines']) > 0) {
                    $numitems = count($data['catlines']);
                    // add leading <ul> tag
                    $data['catlines'][0]['beforetags'] = '<ul>' .
                                               $data['catlines'][0]['beforetags'];
                    // add trailing </ul> tag
                    $data['catlines'][$numitems - 1]['aftertags'] .= '</ul>';
                    // add new column
                    if ($numitems > 7) {
                        $miditem = round(($numitems + 0.5) / 2) - 1;
                        $data['catlines'][$miditem]['aftertags'] .=
                                               '</ul></td><td valign="top"><ul>';
                    }
                }
            }
        }
        break;

    case 1: // tree (side block)
    default:
        $template = 'nav-tree';
        // Get current title
        if (xarVarIsCached('Blocks.categories','title')) {
           $title = xarVarGetCached('Blocks.categories','title');
        }
        if (empty($title)) {
            $modinfo = xarModGetInfo($modid);
            $title = $modinfo['displayname'];
        }
        $blockinfo['title'] = xarML('Browse in #(1)',$title);
        $data['cattrees'] = array();

        if (empty($cids) || count($cids) == 0) {
            foreach ($mastercids as $cid) {
                $catparents = array();
                $catitems = array();
                // Get child categories
                $children = xarModAPIFunc('categories','user','getchildren',
                                         array('cid' => $cid,
                                               'return_itself' => true));
                foreach ($children as $cat) {
                    $label = xarVarPrepForDisplay($cat['name']);
                // TODO: now this is a tricky part...
                    $link = xarModURL($modname,$type,$func,
                                     array('catid' => $cat['cid'],
                                           'itemtype' => $itemtype));
                    if (!empty($catcount[$cat['cid']])) {
                        $count = $catcount[$cat['cid']];
                    } else {
                        $count = 0;
                    }
                    if ($cat['cid'] == $cid) {
                        $catparents[] = array('catlabel' => $label,
                                              'catlink' => $link,
                                              'catcount' => $count);
                    } else {
                        $catitems[] = array('catlabel' => $label,
                                            'catlink' => $link,
                                            'catcount' => $count);
                    }
                }
                $data['cattrees'][] = array('catitems' => $catitems,
                                            'catparents' => $catparents);
            }
        } else {
            foreach ($cids as $cid) {
                $catparents = array();
                $catitems = array();
                // Get category information
                $parents = xarModAPIFunc('categories','user','getparents',
                                        array('cid' => $cid));
                if (empty($parents)) {
                    continue;
                }
            // TODO: do something with parents
                $root = '';
                $parentid = 0;
                foreach ($parents as $id => $info) {
                    if (empty($root)) {
                        $root = xarVarPrepForDisplay($info['name']);
                    }
                    if ($id = $cid) {
                        $parentid = $info['parent'];
                    }
                }
                // yes, this excludes the top-level categories too :-)
                if (empty($parentid) || empty($root)) {
                    $parentid = $cid;
            //        return;
                }
                if (!empty($parents[$parentid])) {
                    $cat = $parents[$parentid];
                    $label = xarVarPrepForDisplay($cat['name']);
                    $link = xarModURL($modname,$type,$func,
                                     array('catid' => $cat['cid'],
                                           'itemtype' => $itemtype));
                    if (!empty($catcount[$cat['cid']])) {
                        $count = $catcount[$cat['cid']];
                    } else {
                        $count = 0;
                    }
                    $catparents[] = array('catlabel' => $label,
                                          'catlink' => $link,
                                          'catcount' => $count);
                }

                // Get sibling categories
                $siblings = xarModAPIFunc('categories','user','getchildren',
                                         array('cid' => $parentid));
                if ($vars['showchildren'] && $parentid != $cid) {
                    // Get child categories
                    $children = xarModAPIFunc('categories','user','getchildren',
                                             array('cid' => $cid));
                }

                // Generate list of sibling categories
                foreach ($siblings as $cat) {
                    $label = xarVarPrepForDisplay($cat['name']);
                    $link = xarModURL($modname,$type,$func,
                                     array('catid' => $cat['cid'],
                                           'itemtype' => $itemtype));
                    if (!empty($catcount[$cat['cid']])) {
                        $count = $catcount[$cat['cid']];
                    } else {
                        $count = 0;
                    }
                    $catchildren = array();
                    if ($cat['cid'] == $cid) {
                        if (empty($itemid) && empty($andcids)) {
                            $link = '';
                        } else {
                            $label .= ' +';
                        }
                        if ($vars['showchildren'] && !empty($children) && count($children) > 0) {
                            foreach ($children as $cat) {
                                $clabel = xarVarPrepForDisplay($cat['name']);
                            // TODO: now this is a tricky part...
                                $clink = xarModURL($modname,$type,$func,
                                                  array('catid' => $cat['cid'],
                                                        'itemtype' => $itemtype));
                                if (!empty($catcount[$cat['cid']])) {
                                    $ccount = $catcount[$cat['cid']];
                                } else {
                                    $ccount = 0;
                                }
                                $catchildren[] = array('clabel' => $clabel,
                                                       'clink' => $clink,
                                                       'ccount' => $ccount);
                            }
                        }
                    }
                    $catitems[] = array('catlabel' => $label,
                                        'catlink' => $link,
                                        'catcount' => $count,
                                        'catchildren' => $catchildren);
                }
                $data['cattrees'][] = array('catitems' => $catitems,
                                            'catparents' => $catparents);
            }
        }
        break;
    }
    $data['blockid'] = $blockinfo['bid'];

    // Populate block info and pass to theme
    $blockinfo['content'] = xarTplBlock('categories',$template,$data);
    if (!empty($blockinfo['content'])) {
        return $blockinfo;
    }
}


/**
 * modify block settings
 */
function categories_navigationblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['layout'])) {
        $vars['layout'] = 1;
    }
    if (empty($vars['showcatcount'])) {
        $vars['showcatcount'] = 0;
    }
    if (empty($vars['showchildren'])) {
        $vars['showchildren'] = 0;
    }

    $vars['layouts'] = array(array('id' => 1,
                                   'name' => 'Tree (Side Block)'),
                             array('id' => 2,
                                   'name' => 'Crumbtrail (Top Block)'),
                             array('id' => 3,
                                   'name' => 'Prev/Next (Bottom Block)'));

    $vars['children'] = array(array('id' => 0,
                                    'name' => xarML('None')),
                              array('id' => 1,
                                    'name' => xarML('Direct children only')),
                              array('id' => 2,
                                    'name' => xarML('All children')));

    $vars['blockid'] = $blockinfo['bid'];
    // Return output
    return xarTplBlock('categories','nav-admin',$vars);
}

/**
 * update block settings
 */
function categories_navigationblock_update($blockinfo)
{
    $vars = array();
    if(!xarVarFetch('layout',       'isset', $vars['layout'],        NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('showcatcount', 'isset', $vars['showcatcount'],  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('showchildren', 'isset', $vars['showchildren'],  NULL, XARVAR_DONT_SET)) {return;}


    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

/**
 * built-in block help/information system.
 */
function categories_navigationblock_help()
{
    return '';
}

?>
