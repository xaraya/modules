<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * show some categories filters in a template
 * @TODO: clean up all those ways to get parameters + better templating
 *
 * @param $args['basecids'] array of base cids that you want to filter for, or
 * @param $args['module'] string module that you want to filter for (default current module)
 * @param $args['itemtype'] integer item type of the module items (default none)
 * @param $args['itemid'] integer item id of the current module item (default none)
 * @param $args['catid'] string current category/categories we're navigating in, or
 * @param $args['cids'] array current category/categories we're navigating in
 * @param $args['showcatcount'] integer show a count per category (0 = no, 1 = local count, 2 = deep count)
 * @param $args['showchildren'] integer show children of the current category (0 = no, 1 = immediate children, 2 = all descendants)
 * @param $args['showempty'] integer show empty categories (0 = no, 1 = yes)
 * @param $args['layout'] string layout to use for the filter (form, tree, ... - default form)
 * @param $args['template'] string override the template that corresponds to this layout (form or tree)
 * @param $args['tplmodule'] string override the module where this template is located (default 'categories')
 * @return string containing the HTML (or other) text to output in the BL template
 */
function categories_userapi_showfilter($args)
{
    extract($args);

    // Allow the template to the over-ridden.
    // This allows different category browsing formats in different places.
    if (!empty($template)) {
        $template_override = $template;
    }

    // Get requested layout
    if (empty($layout)) {
        $layout = 'form';
    }

// TODO: for multi-module pages, we'll need some other reference point(s)
//       (e.g. cross-module categories defined in categories admin ?)
    // Get current module
    if (empty($module)) {
        if (xarVarIsCached('Blocks.categories','module')) {
           $modname = xarVarGetCached('Blocks.categories','module');
        }
        if (empty($modname)) {
            $modname = xarModGetName();
        }
    } else {
        $modname = $module;
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        return xarML('Undefined module in categories filter');
    }

    // Get current item type (if any)
    if (!isset($itemtype)) {
        if (xarVarIsCached('Blocks.categories','itemtype')) {
            $itemtype = xarVarGetCached('Blocks.categories','itemtype');
        } else {
            // try to get itemtype from input
            xarVarFetch('itemtype', 'id', $itemtype, NULL, XARVAR_DONT_SET);
        }
    }
    if (empty($itemtype)) {
        $itemtype = null;
    }
    if (empty($itemid)) {
        $itemid = null;
    }

    if (empty($basecids)) {
        // Get number of categories for this module + item type
        if (!empty($itemtype)) {
            $numcats = (int) xarModGetVar($modname, 'number_of_categories.'.$itemtype);
        } else {
            $numcats = (int) xarModGetVar($modname, 'number_of_categories');
        }
        if (empty($numcats) || !is_numeric($numcats)) {
            // no categories to show here -> return empty output
            return '';
        }

        // Get master cids for this module + item type
        if (!empty($itemtype)) {
            $cidlist = xarModGetVar($modname,'mastercids.'.$itemtype);
        } else {
            $cidlist = xarModGetVar($modname,'mastercids');
        }
        if (empty($cidlist)) {
            // no categories to show here -> return empty output
            return '';
        } else {
            $basecids = explode(';',$cidlist);
            // preserve order of root categories if possible
            //sort($basecids,SORT_NUMERIC);
        }
    } elseif (!is_array($basecids)) {
        $basecids = explode(';',$basecids);
    }

    // See if we need to show a count per category
    if (!isset($showcatcount)) {
        $showcatcount = 0;
    }

    // See if we need to show the children of current categories
    if (!isset($showchildren)) {
        $showchildren = 1;
    }

    // See if we need to show empty categories
    if (!isset($showempty) && empty($showcatcount)) {
        $showempty = 1; // default yes here (otherwise you never see anything by default - duh)
    }

    // Get current category counts (optional array of cid => count)
    if (empty($showcatcount)) {
        $catcount = array();
    } elseif (empty($catcount)) {
        // A 'deep count' sums the totals at each node with the totals of all descendants.
        if ($showcatcount > 1 || empty($showempty)) {
            if (xarVarIsCached('Blocks.categories', 'deepcount')) {
                $deepcount = xarVarGetCached('Blocks.categories', 'deepcount');
            } else {
                $deepcount = xarModAPIFunc(
                    'categories', 'user', 'deepcount',
                    array('modid' => $modid, 'itemtype' => $itemtype)
                );
                xarVarSetCached('Blocks.categories','deepcount', $deepcount);
            }
        }

        if (xarVarIsCached('Blocks.categories', 'catcount')) {
            $catcount = xarVarGetCached('Blocks.categories', 'catcount');
        } else {
            // Get number of items per category (for this module).
            // If showcatcount == 2 then add in all descendants too.

            if ($showcatcount == 1) {
                // We want to display only children category counts.
                $catcount = xarModAPIFunc(
                    'categories','user', 'groupcount',
                    array('modid' => $modid, 'itemtype' => $itemtype)
                );
            } else {
                // We want to display the deep counts.
                $catcount =& $deepcount;
            }

            xarVarSetCached('Blocks.categories', 'catcount', $catcount);
        }
    }

    // Get current categories
    if (xarVarIsCached('Blocks.categories','catid')) {
       $catid = xarVarGetCached('Blocks.categories','catid');
    }
    if (empty($catid)) {
        // try to get catid from input
        xarVarFetch('catid', 'str', $catid, NULL, XARVAR_DONT_SET);
    }
    // turn $catid into $cids array (and set $andcids flag)
    $istree = 0;
    if (!empty($catid)) {
        // if we're viewing all items below a certain category, i.e. catid = _NN
        if (strstr($catid,'_')) {
             $catid = preg_replace('/_/','',$catid);
             $istree = 1;
        }
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
    } elseif (empty($cids)) {
        if (xarVarIsCached('Blocks.categories','cids')) {
            $cids = xarVarGetCached('Blocks.categories','cids');
        }
        if (xarVarIsCached('Blocks.categories','andcids')) {
            $andcids = xarVarGetCached('Blocks.categories','andcids');
        }
        if (empty($cids)) {
            // try to get cids from input
            xarVarFetch('cids',    'isset', $cids,    NULL,  XARVAR_DONT_SET);
            xarVarFetch('andcids', 'isset', $andcids, false, XARVAR_NOT_REQUIRED);
            // for preview of hooked new/modified items
            xarVarFetch('new_cids',    'isset', $newcids,    NULL,  XARVAR_DONT_SET);
            xarVarFetch('modify_cids', 'isset', $modifycids, NULL,  XARVAR_DONT_SET);

            if (!empty($cids)) {
                // found some cids
            } elseif (!empty($newcids)) {
                $cids = $newcids;
            } elseif (!empty($modifycids)) {
                $cids = $modifycids;
            } else {
                $cids = array();
                if ((empty($module) || $module == $modname) && !empty($itemid)) {
                    $links = xarModAPIFunc('categories','user','getlinks',
                                          array('modid' => $modid,
                                                'itemtype' => $itemtype,
                                                'iids' => array($itemid)));
                    if (!empty($links) && count($links) > 0) {
                        $cids = array_keys($links);
                    }
                }
            }
        }
    }
    if (count($cids) > 0) {
        $seencid = array();
        foreach ($cids as $cid) {
            if (empty($cid) || !is_numeric($cid)) {
                continue;
            }
            $seencid[$cid] = 1;
        }
        $cids = array_keys($seencid);
    }

    $data = array();
    $data['cids'] = $cids;
    $data['istree'] = $istree;
    // pass information about current module, item type and item id (if any) to template
    $data['module'] = $modname;
    $data['itemtype'] = $itemtype;
    $data['itemid'] = $itemid;

    switch ($layout) {

        case 'form':
        case 'tree':
        default:
            $template = $layout;
            $data['cattrees'] = array();

            foreach ($basecids as $cid) {
                $catparent = array();
                $catitems = array();
                $startindent = 0;

                // Get child categories
                $children = xarModAPIFunc('categories','user','getcat',
                                          array('cid' => $cid,
                                                'getchildren' => true,
                                                'return_itself' => true));

                foreach ($children as $cat) {
                    if (!empty($catcount[$cat['cid']])) {
                        $count = $catcount[$cat['cid']];
                    } else {
                        $count = 0;

                        // TODO: check! When does this section get executed?
// <mikespub> this is used in the dynamic case, to show the base categories for a module+itemtype
//            when no categories are currently selected
// See also the navigation block, which was supposed to stay in sync with this code, except
// for returning null instead of '', and adding some block title at the end of the code...
                        // TODO: how much duplication is there in these three loops?
                        // Note: when hiding empty categories, check the deep count
                        // as a child category may be empty, but it could still have
                        // descendants with items.

                        if (!empty($showempty) || !empty($deepcount[$cat['cid']])) {
                            // We are not hiding empty categories - set count to zero.
                            $count = 0;
                        } else {
                            // We want to hide empty categories - so skip this loop.
                            continue;
                        }
                    }

                    $label = xarVarPrepForDisplay($cat['name']);

                    if ($cat['cid'] == $cid) {
                        $catparent = array('catlabel' => $label,
                                           'catid' => $cat['cid'],
                                           'catdepth' => 0,
                                           'catcount' => $count,
                                           'catitems' => array());
                        $startindent = $cat['indentation'];
                    } else {
                        $catitems[] = array('catlabel' => $label,
                                            'catid' => $cat['cid'],
                                            'catdepth' => $cat['indentation'] - $startindent,
                                            'catcount' => $count);
                    }
                }
                $data['cattrees'][$cid] = $catparent;
                $data['cattrees'][$cid]['catitems'] = $catitems;
            }
            break;
    }

    // Specify the module where the templates are located
    if (empty($tplmodule)) {
        $tplmodule = 'categories';
    }
    // Do template override.
    if (!empty($template_override)) {
        $template = $template_override;
    }
    return xarTplModule($tplmodule, 'user', 'showfilter', $data, $template);
}

?>
