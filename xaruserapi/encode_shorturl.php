<?php
/**
 * Support for short URLs (user functions)
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the Courses module development team
 * @param  $args the function and arguments passed to xarModURL
 * @return string. Path to be added to index.php for a short URL, or empty if failed
 */
function courses_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);
    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

    /* Check if we have module alias set or not */
    $aliasisset = xarModGetVar('courses', 'useModuleAlias');
    $aliasname = xarModGetVar('courses','aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }

    // Note : make sure you don't pass the following variables as arguments in
    // your module too - adapt here if necessary
    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'courses';
    $alias = xarModGetAlias($module);
    // specify some short URLs relevant to your module
    if ($func == 'main') {
        if (($module == $alias) && ($usealias)){
            $path = '/' . $aliasname . '/';
        } else {
            $path = '/' . $module . '/';
        }
        // Note : if your main function calls some other function by default,
        // you should set the path directly to that other function
    } elseif ($func == 'view') {
      if (($module == $alias) && ($usealias)){
            $path = '/' . $aliasname . '/list.html';
        } else {
            $path = '/' . $module . '/list.html';
        }
        // we'll add the optional $startnum parameter below, as a regular
        // URL parameter
        // you might have some additional parameter that you want to use to
        // create different virtual paths here - for example a category name
        if (!empty($cid) && is_numeric($cid)) {
            // use a cache to avoid re-querying for each URL in the same cat
            static $catcache = array();
            if (xarModAPILoad('categories','user')) {
               if (isset($catcache[$cid])) {
                  $cat = $catcache[$cid];
               } else {
                  $cat = xarModAPIFunc('categories','user','getcatinfo',
                                        array('cid' => $cid));
                   // put the category in cache
                  $catcache[$cid] = $cat;
               }
               if (!empty($cat) && !empty($cat['name'])) {
                   // use the category name as part of the path here
                   $path = '/' . $module . '/' . rawurlencode($cat['name']);
               }
             }
         }
        // if you have some additional parameters that you want to keep as
        // regular URL parameters - example for an array :
        // if (isset($other) && is_array($other) && count($other) > 0) {
        // foreach ($other as $id => $val) {
        // $path .= $join . 'other['.$id.']='.$val;
        // // change the join character (once would be enough)
        // $join = '&';
        // }
        // }
    } elseif ($func == 'display') {
        // check for required parameters
        if (isset($courseid) && is_numeric($courseid)) {
            if (($module == $alias) && ($usealias)){
                $path = '/' . $aliasname . '/'. $courseid . '.html';
            } else {
                $path = '/' . $module . '/' . $courseid . '.html';
            }
            // you might have some additional parameter that you want to use to
            // create different virtual paths here - for example a category name
            // See above for an example...
        } else {
            // we don't know how to handle that -> don't create a path here
            // Note : this generally means that someone tried to create a
            // link to your module, but used invalid parameters for xarModURL
            // -> you might want to provide a default path to return to
            // $path = '/' . $module . '/list.html';
        }
    } else {
        // anything else that you haven't defined a short URL equivalent for
        // -> don't create a path here
    }
    // add some other module arguments as standard URL parameters
    if (!empty($path)) {
        if (isset($startnum)) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&';
        }
        if (isset($sortby)) {
            $path .= $join . 'sortby=' . $sortby;
            $join = '&';
        }
        if (!empty($catid)) {
            $path .= $join . 'catid=' . $catid;
            $join = '&';
        } elseif (!empty($cids) && count($cids) > 0) {
            if (!empty($andcids)) {
                $catid = join('+', $cids);
            } else {
                $catid = join('-', $cids);
            }
            $path .= $join . 'catid=' . $catid;
            $join = '&';
        }
    }

    return $path;
}
?>