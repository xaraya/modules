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

    $module = 'xarbb';
    $alias = xarModGetAlias($module);

    $aliasisset = xarModGetVar('xarbb', 'useModuleAlias');
    $aliasname = xarModGetVar('xarbb', 'aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias = true;
    } else{
        $usealias = false;
    }

    // The components of the path.
    $path = array();
    $get = $args;

    // Set the first part of the path, which will always be the 
    // module name or alias.
    if (($module == $alias) && ($usealias)){
        $path[] = $aliasname;
    } else{
        $path[] = $module;
    }

    if ($func == 'main') {
        unset($get['func']);
    } elseif ($func == 'viewforum') {
        if (isset($fid) && is_numeric($fid)) {
            unset($get['func']);
            unset($get['fid']);
            $path[] = 'forum';
            $path[] = $fid;
        }
    } elseif ($func == 'viewtopic') {
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
            unset($get['func']);
            unset($get['tid']);
            $path[] = 'topic';
            $path[] = $tid;
        }
    } elseif ($func == 'newtopic') {
        // check for required parameters
        if (isset($fid) && is_numeric($fid)) {
            unset($get['func']);
            if (isset($phase) && $phase == 'quote' && isset($tid) && is_numeric($tid)){
                unset($get['tid']);
                $path[] = 'newreply';
                $path[] = $tid;
                $path[] = 'quote';
            } else {
                unset($get['fid']);
                $path[] = 'newtopic';
                $path[] = $fid;
            }
        } elseif (isset($tid) && is_numeric($tid)) {
            unset($get['func']);
            unset($get['tid']);
            $path[] = 'newtopic';
            $path[] = $tid;
            $path[] = 'edit';
        }
    } elseif ($func == 'newreply') {
        unset($get['func']);
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
            $path[] = 'newreply';
            $path[] = $tid;
            unset($get['tid']);
            if (isset($phase) && $phase == 'edit' && isset($cid) && is_numeric($cid)) {
                unset($get['cid']);
                $path[] = 'edit';
                $path[] = $cid;
            } elseif (isset($phase) && $phase == 'quote' && isset($cid) && is_numeric($cid)) {
                unset($get['cid']);
                $path[] = 'quote';
                $path[] = $cid;
            } elseif (isset($phase) && $phase == 'quote') {
                $path[] = 'quote';
            }
        }
    } elseif ($func == 'updatetopic') {
        unset($get['func']);
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
            unset($get['tid']);
            $path[] = 'updatetopic';
            $path[] = $tid;
        }
    } elseif ($func == 'subscribe') {
        unset($get['func']);
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
            unset($get['tid']);
            $path[] = 'subscribe';
            $path[] = $tid;
        }
    } elseif ($func == 'unsubscribe') {
        unset($get['func']);
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
            unset($get['tid']);
            $path[] = 'unsubscribe';
            $path[] = $tid;
        }
    } elseif ($func == 'printtopic') {
        unset($get['func']);
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
            unset($get['tid']);
            $path[] = 'printtopic';
            $path[] = $tid;
            // We will ensure the 'theme' get parameter is set.
            $get['theme'] = 'print';
        }
    }

    return array('path' => $path, 'get' => $get);
} 

?>