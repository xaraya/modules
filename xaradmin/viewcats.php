<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * View the categories in the system
 *
 * @param pagerstart
 * @param catsperpage
 * @param useJSdisplay
 * @todo MichelV: do we need the reference creations in here?
 */
function categories_admin_viewcats()
{
    // Security check
    if(!xarSecurityCheck('ReadCategories')) return;

    $data = array();

    $categories = xarModAPIFunc('categories',
                                'user',
                                'getcat',
                                array('cid' => false,
                                      'getchildren' => true,
                                      'usecache' => false));

    if (empty($categories)) {
        return xarTplModule('categories','admin','viewcats-nocats',$data);
    }

    $catstree = array();

    $categories = array_reverse($categories, true);

    $parentid = null;
    $children = array(0 => array());

    // convert the flat cats structure into a nested one for display
    foreach ($categories as $tmpcat) {
        $cid = $tmpcat['cid'];
        $parent = $tmpcat['parent'];
        $left = $tmpcat['left'];
        if ($parent !== $parentid) {
            if (isset($children[$parent]) && count($children[$parent]) > 0) {
                ksort($children[$parent], SORT_NUMERIC);
                $tmpcat['children'] = $children[$cid];
            } else {
                $children[$parent] = array();
            }
            $parentid = $parent;
            $children[$parent][$left] = $tmpcat;
            ksort($children[$parent], SORT_NUMERIC);
        } else {
            $children[$parent][$left] = $tmpcat;
            ksort($children[$parent], SORT_NUMERIC);
        }
        if ($parent == 0) {
            if(isset($children[$cid]) && count($children[$cid]) > 0) {
                ksort($children[$cid], SORT_NUMERIC);
                $tmpcat['children'] = $children[$cid];
            }
            $catstree[$left] = $tmpcat;
        }
    }
    ksort($catstree, SORT_NUMERIC);

    $data['cats'] = $catstree;

    return xarTplModule('categories','admin','viewcats-render',$data);
}

?>
