<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
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
 * - http://mysite.com/index.php/newsletter/ (main function)
 * - http://mysite.com/index.php/newsletter/list.html (view function)
 * - http://mysite.com/index.php/newsletter/123.html (display function)
 *
 * in addition to the 'normal' Xaraya URLs that look like :
 *
 * - http://mysite.com/index.php?module=newsletter&func=display&exid=123
 *
 * You can also combine the two, e.g. for less frequently-used parameters :
 *
 * - http://mysite.com/index.php/newsletter/list.html?startnum=21
 *
 *
 * Module developers who wish to support this feature are strongly
 * recommended to create virtual paths that are 'semantically meaningful',
 * so that people navigating in your module can understand at a glance what
 * the short URLs mean, and how they could e.g. display item 234 simply
 * by changing the 123.html into 234.html.
 *
 * For newer modules with many different optional parameters and functions,
 * this generally implies re-thinking which parameters could easily be set
 * to some default to cover the most frequently-used cases, and rethinking
 * how each function could be represented inside some "virtual directory
 * structure". E.g. .../archive/2002/05/, .../forums/12/345.html, ../recent.html
 * or .../<categoryname>/123.html
 *
 * The same kind of encoding/decoding can be done for admin functions as well,
 * except that by default, the URLs will start with index.php/admin/newsletter.
 * The encode/decode functions for admin functions are in xaradminapi.php.
 *
 */

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author Richard Cave
 * @param $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function newsletter_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

    // Note : make sure you don't pass the following variables as arguments in
    // your module too - adapt here if necessary

    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'newsletter';

    // specify some short URLs relevant to your module
    if ($func == 'main') {
        $path = '/' . $module . '/';

        // Note : if your main function calls some other function by default,
        // you should set the path to directly to that other function

    } elseif ($func == 'newsubscription') {
        $path = '/' . $module . '/subscribe';

    } elseif ($func == 'modifysubscription') {
        $path = '/' . $module . '/modify';

        // we'll add the optional $startnum parameter below, as a regular
        // URL parameter

        // you might have some additional parameter that you want to use to
        // create different virtual paths here - for newsletter a category name
        // if (!empty($cid) && is_numeric($cid)) {
        //     // use a cache to avoid re-querying for each URL in the same cat
        //     static $catcache = array();
        //     if (xarModAPILoad('categories','user')) {
        //         if (isset($catcache[$cid])) {
        //             $cat = $catcache[$cid];
        //         } else {
        //             $cat = xarModAPIFunc('categories','user','getcatinfo',
        //                                 array('cid' => $cid));
        //             // put the category in cache
        //             $catcache[$cid] = $cat;
        //         }
        //         if (!empty($cat) && !empty($cat['name'])) {
        //             // use the category name as part of the path here
        //             $path = '/' . $module . '/' . rawurlencode($cat['name']);
        //         }
        //     }
        // }

        // if you have some additional parameters that you want to keep as
        // regular URL parameters - newsletter for an array :
        // if (isset($other) && is_array($other) && count($other) > 0) {
        //     foreach ($other as $id => $val) {
        //        $path .= $join . 'other['.$id.']='.$val;
        //        // change the join character (once would be enough)
        //        $join = '&';
        //     }
        // }

    } elseif ($func == 'viewarchives') {
        // check for required parameters
        if (isset($publicationId) && is_numeric($publicationId)) {
            $path = '/' . $module . '/archives/' . $publicationId;

            // you might have some additional parameter that you want to use to
            // create different virtual paths here - for newsletter a category name
            // See above for an newsletter...

        } else {
            // we don't know how to handle that -> don't create a path here

            // Note : this generally means that someone tried to create a
            // link to your module, but used invalid parameters for xarModURL
            // -> you might want to provide a default path to return to
            $path = '/' . $module . '/archives';
        }

    } elseif ($func == 'previewissue') {
        // check for required parameters
        if (isset($issueId) && is_numeric($issueId)) {
            $path = '/' . $module . '/preview/' . $issueId;

            // you might have some additional parameter that you want to use to
            // create different virtual paths here - for newsletter a category name
            // See above for an newsletter...

        } else {
            // we don't know how to handle that -> don't create a path here

            // Note : this generally means that someone tried to create a
            // link to your module, but used invalid parameters for xarModURL
            // -> you might want to provide a default path to return to
            $path = '/' . $module . '/preview';
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
        if (isset($numitems)) {
            $path .= $join . 'numitems=' . $numitems;
            $join = '&';
        }
        if (isset($phase)) {
            $path .= $join . 'phase=' . $phase;
            $join = '&';
        }
        if (isset($display)) {
            $path .= $join . 'display=' . $display;
            $join = '&';
        }
        if (isset($owner)) {
            $path .= $join . 'owner=' . $owner;
            $join = '&';
        }
        if (isset($sortby)) {
            $path .= $join . 'sortby=' . $sortby;
            $join = '&';
        }
    }

    return $path;
}

?>
