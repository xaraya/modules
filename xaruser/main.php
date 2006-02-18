<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
*/

/**
 * display keywords entries
 * @return mixed bool and redirect to url
 */
function keywords_user_main($args)
{
if (!xarSecurityCheck('ReadKeywords')) return;

    xarVarFetch('keyword','str',$keyword,'', XARVAR_DONT_SET);
    xarVarFetch('id','id',$id,'', XARVAR_DONT_SET);
    xarVarFetch('tab','int:0:5',$tab,'0', XARVAR_DONT_SET);

    //extract($args);
    $displaycolumns= xarModGetVar('keywords','displaycolumns');
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
        // get the list of items to which this keyword is assigned
        $items = xarModAPIFunc('keywords','user','getitems',
                               array('keyword' => $keyword));

        if (!isset($items)) return;

        // build up a list of item ids per module & item type
        $modules = array();
        foreach ($items as $id => $item) {
             if (!isset($modules[$item['moduleid']])) {
                 $modules[$item['moduleid']] = array();
             }
             if (empty($item['itemtype'])) {
                 $item['itemtype'] = 0;
             }
             if (!isset($modules[$item['moduleid']][$item['itemtype']])) {
                 $modules[$item['moduleid']][$item['itemtype']] = array();
             }
             $modules[$item['moduleid']][$item['itemtype']][$item['itemid']] = $id;
        }

        // get the corresponding URL and title (if any)
        foreach ($modules as $moduleid => $itemtypes) {
            $modinfo = xarModGetInfo($moduleid);
            if (!isset($modinfo) || empty($modinfo['name'])) return;
            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
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

    xarResponseRedirect($url);

    return true;
}

?>
