<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */

/**
 * display keywords entries
 * @return mixed bool and redirect to url
 */
function keywords_user_view($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return;

    if (!xarVarFetch('keyword', 'pre:trim:str:1:',
        $keyword, null, XARVAR_DONT_SET)) return;

    if (!xarVarFetch('startnum', 'int:1:',
        $startnum, null, XARVAR_NOT_REQUIRED)) return;

    $data = array();

    if (!empty($keyword)) {
        $items_per_page = xarModVars::get('keywords', 'items_per_page', 20);
        $total = xarMod::apiFunc('keywords', 'words', 'countitems',
            array(
                //'module_id' => $module_id,
                //'itemtype' => $itemtype,
                'keyword' => $keyword,
                'skip_restricted' => true,
            ));
        $items = xarMod::apiFunc('keywords', 'words', 'getitems',
            array(
                //'module_id' => $module_id,
                //'itemtype' => $itemtype,
                'keyword' => $keyword,
                'skip_restricted' => true,
                'startnum' => $startnum,
                'numitems' => $items_per_page,
            ));

        $modlist = xarMod::apiFunc('keywords', 'words', 'getmodulecounts',
            array(
                'skip_restricted' => true,
            ));
        $modtypes = array();
        $modules = array();
        foreach ($modlist as $module => $itemtypes) {
            $modules[$module] = xarMod::getBaseInfo($module);
            $modules[$module]['itemtypes'] = $itemtypes;
            if (!isset($modtypes[$module])) {
                try {
                    $modtypes[$module] = xarMod::apiFunc($module, 'user', 'getitemtypes');
                } catch (Exception $e) {
                    $modtypes[$module] = array();
                }
            }
            foreach ($itemtypes as $typeid => $typeinfo) {
                if (empty($typeid)) continue;
                if (!isset($modtypes[$module][$typeid])) {
                    $modtypes[$module][$typeid] = array(
                        'label' => xarML('Itemtype #(1)', $typeid),
                        'title' => xarML('View itemtype #(1) items', $typeid),
                        'url' => xarModURL($module, 'user', 'view', array('itemtype' => $typeid)),
                    );
                }
                $modules[$module]['itemtypes'][$typeid] += $modtypes[$module][$typeid];
            }
        }

        $seenitems = array();
        foreach ($items as $item) {
            if (!isset($seenitems[$item['module']]))
                $seenitems[$item['module']] = array();
            if (!isset($seenitems[$item['module']][$item['itemtype']]))
                $seenitems[$item['module']][$item['itemtype']] = array();
            $seenitems[$item['module']][$item['itemtype']][$item['itemid']] = $item;
        }
        foreach ($seenitems as $module => $itemtypes) {
            $modules[$module]['itemlinks'] = array();
            foreach ($itemtypes as $typeid => $itemids) {
                $modules[$module]['itemlinks'][$typeid] = $itemids;
                try {
                    $itemlinks = xarMod::apiFunc($module, 'user', 'getitemlinks',
                        array(
                            'itemtype' => $typeid,
                            'itemids' => array_keys($itemids),
                        ));
                } catch (Exception $e) {
                    $itemlinks = array();
                }
                foreach (array_keys($itemids) as $id) {
                    if (!isset($itemlinks[$id])) {
                        $itemlinks[$id] = array(
                            'label' => xarML('Item #(1)', $id),
                            'title' => xarML('Display Item #(1)', $id),
                            'url' => xarModURL($module, 'user', 'display',
                                array('itemtype' => !empty($itemtype) ? $itemtype : null, 'itemid' => $id)),
                        );
                    }
                    $modules[$module]['itemlinks'][$typeid][$id] += $itemlinks[$id];
                }
            }
        }
        $data['modules'] = $modules;
        $data['items_per_page'] = $items_per_page;
        $data['total'] = $total;
        $data['items'] = $items;
        $data['use_icons'] = xarModVars::get('keywords', 'use_module_icons');
    } else {
        $user_layout = xarModVars::get('keywords', 'user_layout', 'list');

        switch ($user_layout) {
            case 'list':
            default:
                $cols_per_page = xarModVars::get('keywords', 'cols_per_page', 2);
                $items_per_page = xarModVars::get('keywords', 'words_per_page', 50);
                $total = xarMod::apiFunc('keywords', 'words', 'countwords',
                    array(
                        'skip_restricted' => true,
                    ));
                $items = xarMod::apiFunc('keywords', 'words', 'getwordcounts',
                    array(
                        'startnum' => $startnum,
                        'numitems' => $items_per_page,
                        'skip_restricted' => true,
                    ));
                $data['cols_per_page'] = $cols_per_page;
                $data['items_per_page'] = $items_per_page;
                $data['total'] = $total;
                $data['items'] = $items;
            break;
            case 'cloud':
                $items = xarMod::apiFunc('keywords', 'words', 'getwordcounts',
                    array(
                        'skip_restricted' => true,
                    ));
                $counts = array();
                foreach ($items as $item)
                    $counts[$item['keyword']] = $item['count'];
                $font_min = xarModVars::get('keywords', 'cloud_font_min');
                $font_max = xarModVars::get('keywords', 'cloud_font_max');
                $font_unit = xarModVars::get('keywords', 'cloud_font_unit');
                $min_count = min($counts);
                $max_count = max($counts);
                $range = $max_count - $min_count;
                if ($range <= 0)
                    $range = 1;
                $font_range = $font_min - $font_max;
                if ($font_range <= 0)
                    $font_range = 1;
                $range_step = $font_range/$range;
                foreach ($items as $k => $item) {
                    $count = $counts[$item['keyword']];
                    $items[$k]['weight'] = $font_min + ( ( $count - $min_count ) * $range_step );
                }
                $data['items'] = $items;
                $data['unit'] = $font_unit;

            break;
        }
        $data['user_layout'] = $user_layout;
    }

    $data['startnum'] = $startnum;
    $data['keyword'] = $keyword;

    return $data;

    xarVarFetch('keyword','str',$keyword,'', XARVAR_DONT_SET);
    xarVarFetch('id','id',$id,'', XARVAR_DONT_SET);
    xarVarFetch('tab','int:0:5',$tab,'0', XARVAR_DONT_SET);

    //extract($args);
    $displaycolumns= xarModVars::get('keywords','displaycolumns');
    if (!isset($displaycolumns) or (empty($displaycolumns))){
        $displaycolumns=1;
    }

    if (empty($keyword)) {
        // get the list of keywords that are in use
        $words = xarModAPIFunc('keywords','user','getlist',
                               array('count' => 1,
                                     'tab' => $tab));

        $items = array();
        foreach ($words as $word => $count) {
            if (empty($word)) continue;
            $items[] = array(
                'url' => xarModURL(
                    'keywords', 'user', 'main', array('keyword' => $word)
                ),
                'label' => xarVarPrepForDisplay($word),
                'count' => $count
            );
        }

        return array('status' => 0,
                     'displaycolumns' => $displaycolumns,
                     'items' => $items,
                     'tab' => $tab);

    } elseif (empty($id)) {
        // @checkme: necessary to decode? already done by php?
        $keyword = rawurldecode($keyword);
        // @checkme: we don't replace spaces with underscores when constructing links
        if (strpos($keyword,'_') !== false) {
            $keyword = str_replace('_',' ',$keyword);
        }
        // get the list of items to which this keyword is assigned
        $items = xarModAPIFunc('keywords','user','getitems',
                               array('keyword' => $keyword));

        if (!isset($items)) return;

        // build up a list of item ids per module & item type
        $modules = array();
        foreach ($items as $id => $item) {
             if (!isset($modules[$item['module_id']])) {
                 $modules[$item['module_id']] = array();
             }
             if (empty($item['itemtype'])) {
                 $item['itemtype'] = 0;
             }
             if (!isset($modules[$item['module_id']][$item['itemtype']])) {
                 $modules[$item['module_id']][$item['itemtype']] = array();
             }
             $modules[$item['module_id']][$item['itemtype']][$item['itemid']] = $id;
        }

        // get the corresponding URL and title (if any)
        foreach ($modules as $moduleid => $itemtypes) {
            $modinfo = xarModGetInfo($moduleid);
            if (!isset($modinfo) || empty($modinfo['name'])) return;

            // Get the list of all item types for this module (if any)
            try {
                $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes');
            } catch (Exception $e) {
                $mytypes = array();
            }

            foreach ($itemtypes as $itemtype => $itemlist) {
                $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                                           array('itemtype' => $itemtype,
                                                 'itemids' => array_keys($itemlist)),
                                           0);
                foreach ($itemlist as $itemid => $id) {
                    if (!isset($items[$id])) continue;
                    if (isset($itemlinks) && isset($itemlinks[$itemid])) {
                        $items[$id]['url'] = $itemlinks[$itemid]['url'];
                        $items[$id]['label'] = $itemlinks[$itemid]['label'];
                    } else {
                        $items[$id]['url'] = xarModURL($modinfo['name'],'user','display',
                        //$items[$id]['url'] = xarModURL($modinfo['name'],'user','main',
                                                       array('itemtype' => $itemtype,
                                                             'itemid' => $itemid));
                         // you could skip those in the template
                    }
                    if (!empty($itemtype)) {
                        if (isset($mytypes) && isset($mytypes[$itemtype])) {
                            $items[$id]['modname'] = $mytypes[$itemtype]['label'];
                        } else {
                            $items[$id]['modname'] = ucwords($modinfo['name']) . ' ' . $itemtype;
                        }
                    } else {
                        $items[$id]['modname'] = ucwords($modinfo['name']);
                    }
                }
            }
        }
        unset($modules);

        return array('status' => 1,
                     'displaycolumns' => $displaycolumns,
                     'keyword' => xarVarPrepForDisplay($keyword),
                     'items' => $items);
    }

    // @checkme: what's all this?
    // if we're given an id we redirect to item display?
    // we already got a link pointing to the item display url, why isn't that used
    // in the template instead of pointing here?
    $items = xarModAPIFunc(
        'keywords','user','getitems',
        array('keyword' => $keyword,
        'id' => $id)
    );
    if (!isset($items)) return;
    if (!isset($items[$id])) {
        return array('status' => 2);
    }

    $item = $items[$id];
    if (!isset($item['moduleid'])) {
        return array('status' => 2);
    }

    $modinfo = xarModGetInfo($item['moduleid']);
    if (!isset($modinfo) || empty($modinfo['name'])) {
        return array('status' => 3);
    }

// TODO: make configurable per module/itemtype
    $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                               array('itemtype' => $item['itemtype'],
                                     'itemids' => array($item['itemid'])),
                               0);
    if (isset($itemlinks[$item['itemid']]) && !empty($itemlinks[$item['itemid']]['url'])) {
        $url = $itemlinks[$item['itemid']]['url'];
    } else {
        $url = xarModURL($modinfo['name'],'user','display',
                         array('itemtype' => $item['itemtype'],
                               'itemid' => $item['itemid']));
    }

    xarController::redirect($url);

    return true;
}

?>