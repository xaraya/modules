<?php
/**
 * Standard function to encode short urls for a module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
 * @author Jo Dalle Nogare
*/

/*
 * Support for short URLs (user functions)
 *
 * The following two functions encode module parameters into some
 * virtual path that will be added to index.php, and decode a virtual
 * path back to the original module parameters.
 *
 * The result is that people (and search engines) can use URLs like :
 *
 * - http://mysite.com/index.php/xarbb/ (main function)
 * - http://mysite.com/index.php/xarbb/list.html (view function)
 * - http://mysite.com/index.php/xarbb/123.html (display function)
 *
 * in addition to the 'normal' Xaraya URLs that look like :
 *
 * - http://mysite.com/index.php?module=xarbb&func=display&exid=123
 *
 * You can also combine the two, e.g. for less frequently-used parameters :
 *
 * - http://mysite.com/index.php/xarbb/list.html?startnum=21
 *
 *
 * Module developers who wish to support this feature are strongly
 * recommended to create virtual paths that are 'semantically meaningful',
 * so that people navigating in your module can understand at a glance what
 * the short URLs mean, and how they could e.g. display item 234 simply
 * by changing the 123.html into 234.html.
 *
 * For older modules with many different optional parameters and functions,
 * this generally implies re-thinking which parameters could easily be set
 * to some default to cover the most frequently-used cases, and rethinking
 * how each function could be represented inside some "virtual directory
 * structure". E.g. .../archive/2002/05/, .../forums/12/345.html, ../recent.html
 * or .../<categoryname>/123.html
 *
 * The same kind of encoding/decoding can be done for admin functions as well,
 * except that by default, the URLs will start with index.php/admin/xarbb.
 * The encode/decode functions for admin functions are in xaradminapi.php.
 *
 */

/**
 * return the path for a short URL to xarModURL for this module
 * 
 * @author the xarBB module development team 
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function xarbb_userapi_encode_shorturl($args)
{ 
    // Get arguments from argument array
    extract($args); 
    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }
   // Coming from categories etc.
    if (!empty($objectid)) {
        $fid = $objectid;
    }

    $aliasisset = xarModGetVar('xarbb', 'useModuleAlias');
    $aliasname = xarModGetVar('xarbb','aliasname');
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
    $module = 'xarbb';

    $alias = xarModGetAlias($module);

    // specify some short URLs relevant to your module
    if ($func == 'main') {
        if (($module == $alias) && ($usealias)){
        // OK, we can use a 'fake' module name here
            $path = '/' . $aliasname . '/';
        } else{
            $path = '/' . $module . '/';
        }
        // Note : if your main function calls some other function by default,
        // you should set the path to directly to that other function
    } elseif ($func == 'viewforum') {
        if (isset($fid) && is_numeric($fid)) {
            if (($module == $alias) && ($usealias)){
                $path = '/' . $aliasname . '/forum/' . $fid;
            } else {
                $path = '/' . $module . '/forum/' . $fid;
            }
        // we'll add the optional $startnum parameter below, as a regular
        // URL parameter
        // you might have some additional parameter that you want to use to
        // create different virtual paths here - for example a category name
        // if (!empty($cid) && is_numeric($cid)) {
        // // use a cache to avoid re-querying for each URL in the same cat
        // static $catcache = array();
        // if (xarModAPILoad('categories','user')) {
        // if (isset($catcache[$cid])) {
        // $cat = $catcache[$cid];
        // } else {
        // $cat = xarModAPIFunc('categories','user','getcatinfo',
        // array('cid' => $cid));
        // // put the category in cache
        // $catcache[$cid] = $cat;
        // }
        // if (!empty($cat) && !empty($cat['name'])) {
        // // use the category name as part of the path here
        // $path = '/' . $module . '/' . rawurlencode($cat['name']);
        // }
        // }
        // }
        // if you have some additional parameters that you want to keep as
        // regular URL parameters - example for an array :
        // if (isset($other) && is_array($other) && count($other) > 0) {
        // foreach ($other as $id => $val) {
        // $path .= $join . 'other['.$id.']='.$val;
        // // change the join character (once would be enough)
        // $join = '&';
        // }
        // }
        } else {
            // we don't know how to handle that -> don't create a path here
            // Note : this generally means that someone tried to create a
            // link to your module, but used invalid parameters for xarModURL
            // -> you might want to provide a default path to return to
            // $path = '/' . $module . '/list.html';
        } 
    } elseif ($func == 'viewtopic') {
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
          if (($module == $alias) && ($usealias)){
                 $path = '/' . $aliasname . '/topic/' . $tid;
            } else {
                $path = '/' . $module . '/topic/' . $tid;
            }
        } else {
            // we don't know how to handle that -> don't create a path here

        }
    } elseif ($func == 'newtopic') {
        // check for required parameters
        if (isset($fid) && is_numeric($fid)) {
            if (isset($phase) && $phase == 'quote' && isset($tid) && is_numeric($tid)){
                if (($module == $alias) && ($usealias)){
                       $path = '/' . $aliasname . '/newreply/'.$tid.'/quote/';
                } else {
                      $path = '/' . $module . '/newreply/'.$tid.'/quote/';
                }
           }else {
                if (($module == $alias) && ($usealias)){
                       $path = '/' . $aliasname . '/newtopic/' . $fid;
                } else {
                      $path = '/' . $module . '/newtopic/' . $fid;
                }
            }
       } elseif (isset($tid) && is_numeric($tid)) {
              if (($module == $alias) && ($usealias)){
                       $path = '/' . $aliasname . '/newtopic/'.$tid.'/edit/';
                } else {
                      $path = '/' . $module . '/newtopic/'.$tid.'/edit/';
                }
        } else {
        }
   } elseif ($func == 'newreply') {
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
            if (isset($phase) && $phase == 'edit' && isset($cid) && is_numeric($cid)) {
                if (($module == $alias) && ($usealias)){
                       $path = '/' . $aliasname . '/newreply/'.$tid.'/edit/' . $cid;
                } else {
                     $path = '/' . $module . '/newreply/'.$tid.'/edit/' . $cid;
                }
            }elseif (isset($phase) && $phase == 'quote' && isset($cid) && is_numeric($cid)){
                if (($module == $alias) && ($usealias)){
                       $path = '/' . $aliasname . '/newreply/'.$tid.'/quote/' . $cid;
                } else {
                     $path = '/' . $module . '/newreply/'.$tid.'/quote/' . $cid;
                }
            }elseif (isset($phase) && $phase == 'quote') {
               if (($module == $alias) && ($usealias)){
                       $path = '/' . $aliasname . '/newreply/'.$tid.'/quote/';
                } else {
                     $path = '/' . $module . '/newreply/'.$tid.'/quote/';
                }
            } else {
                if (($module == $alias) && ($usealias)){
                       $path = '/' . $aliasname . '/newreply/' . $tid;
                } else {
                     $path = '/' . $module . '/newreply/' . $tid;
                }
            }
        } else {
         }
  } elseif ($func == 'updatetopic') {
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
          if (($module == $alias) && ($usealias)){
                 $path = '/' . $aliasname . '/updatetopic/' . $tid;
            } else {
                $path = '/' . $module . '/updatetopic/' . $tid;
            }
         } else {
            // we don't know how to handle that -> don't create a path here
        }
 } elseif ($func == 'subscribe') {
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
          if (($module == $alias) && ($usealias)){
                 $path = '/' . $aliasname . '/subscribe/' . $tid;
            } else {
                $path = '/' . $module . '/subscribe/' . $tid;
            }
        } else {
            // we don't know how to handle that -> don't create a path here
        }
 } elseif ($func == 'unsubscribe') {
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
          if (($module == $alias) && ($usealias)){
                 $path = '/' . $aliasname . '/unsubscribe/' . $tid;
            } else {
                $path = '/' . $module . '/unsubscribe/' . $tid;
            }
        } else {
            // we don't know how to handle that -> don't create a path here
        }
 } elseif ($func == 'printtopic') {
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
          if (($module == $alias) && ($usealias)){
                 $path = '/' . $aliasname . '/printtopic/' . $tid.'?theme=print';
            } else {
                $path = '/' . $module . '/printtopic/' . $tid.'?theme=print';
            }
        } else {
            // we don't know how to handle that -> don't create a path here
        }
    } else {
        // anything else that you haven't defined a short URL equivalent for
        // -> don't create a path here
    }
    // add some other module arguments as standard URL parameters
    if (!empty($path)) {
        if (isset($startnum) && $startnum != 1) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&';
        }
        if (isset($read)) {
            $path .= $join . 'read=' . $read;
            $join = '&';
        } 
        if (isset($view)) {
            $path .= $join . 'view=' . $view;
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
