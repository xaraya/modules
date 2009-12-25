<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
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
                                      'getchildren' => true));

    if (empty($categories)) {
        return xarTplModule('categories','admin','viewcats-nocats',$data);
    }

    $catstree = array();

    $categories = array_reverse($categories, true);

    $parentid = null;
    $siblings = array();

    // convert the flat cats structure into a nested one for display
    foreach ($categories as $tmpcat) {
        if ($tmpcat['parent'] !== $parentid) {
            if (count($siblings) > 0) {
                krsort($siblings);
                $tmpcat['children'] = array_reverse($siblings);
            }
            $siblings = array();
            $parentid = $tmpcat['parent'];
            $siblings[$tmpcat['left']] = $tmpcat;
        } else {
            $siblings[$tmpcat['left']] = $tmpcat;
        }
        if ($tmpcat['parent'] == 0) {
            $catstree[$tmpcat['left']] = $tmpcat;
            $siblings = array();
        }
    }
    ksort($catstree);

    $data['cats'] = $catstree;

    return xarTplModule('categories','admin','viewcats-render',$data);
}

?>
