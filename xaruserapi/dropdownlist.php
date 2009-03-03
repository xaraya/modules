<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * get categories
 *
 * @param int $args['indexby'] set to 'cid' to return category cid values for each option instead of names
 * @param int $args['cid'] restrict output only to this category ID and its sibbling (default none)
 * @param int $args['eid'] do not output this category and its sibblings (default none)
 * @param int $args['maximum_depth'] return categories with the given depth or less
 * @param int $args['minimum_depth'] return categories with the given depth or more
 * @param $args['indexby'] =string= specify the index type for the result array (default 'default')
 *  They only change the output IF 'cid' is set:
 *    @param $args['getchildren'] =Boolean= get children of category (default false)
 *    @param $args['getparents'] =Boolean= get parents of category (default false)
 *    @param $args['return_itself'] =Boolean= return the cid itself (default false)
 * @return array Array of categories, or =Boolean= false on failure

 * Examples:
 *    getcat() => Return all the categories
 *    getcat(Array('cid' -> ID)) => Only cid and its children, grandchildren and
 *                                   every other sibbling will be returned
 *    getcat(Array('eid' -> ID)) => All categories will be returned EXCEPT
 *                                   eid and its children, grandchildren and
 *                                   every other sibbling will be returned
 */
function categories_userapi_dropdownlist($args)
{
    extract($args);
    
    $categorylist = xarModAPIFunc('categories', 'user', 'getcat', $args);
    
    if($categorylist === false) return;
    
    $categories = array();
    
    $index = -1;
    foreach($categorylist as $cid => $category) {

        if (isset($indexby) && $indexby == 'cid') {
            $index = $cid;
        } else {
            $index++;
        }
        $indent = "";
        for($x=1;$x<$category['indentation'];$x++) {
            $indent = $indent."&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $categories[$index] = $indent.$category['name'];
    }
    
    return $categories;
}

?>
