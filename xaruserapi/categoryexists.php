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
 * Find out if the category exists from a $path
 *
 * @param $args['cid'] id of category to get children for, or
 * @param $args['cids'] array of category ids to get children for
 * @param $args['return_itself'] =Boolean= return the cid itself (default false)
 * @returns array
 * @return array of category info arrays, false on failure
 */
function categories_userapi_categoryexists( $args )
{
    extract($args);

    $path_array = explode("/", $path);

    $args = array();
    $cid = false;

    $maximum_depth = 2;
    $minimum_depth = 1;

    foreach ($path_array as $cat_name) {

        // Getting categories Array
        $categories = xarModAPIFunc('categories','user','getcat',Array
            (
                'eid'           => false,
                'cid'           => $cid,
                'return_itself' => false,
                'getchildren'   => true,
                'maximum_depth' => $maximum_depth,
                'minimum_depth' => $minimum_depth
            ));
        foreach ($categories as $category) {
            if ($category['name'] == $cat_name) {
                //Found the category we are looking for
                array_shift($path_array);
                $cid = $category["cid"];
            }
        }

        $maximum_depth++;
        $minimum_depth++;
    }

    if (count($path_array) == 0) { return $cid; }

    return false;
}

?>
