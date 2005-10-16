<?php
/**
 * Encode module parameters for Short URL support
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarlinkme
 * @link http://xaraya.com/index.php/release/889.html
 * @author jojodee <jojodee@xaraya.com>
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
 * - http://mysite.com/index.php/xarlinkme/ (main function)
 *
 * in addition to the 'normal' Xaraya URLs that look like :
 *
 * - http://mysite.com/index.php?module=xarlinkme&func=display&bnid=123
 *
 * You can also combine the two, e.g. for less frequently-used parameters :
 *
 * - http://mysite.com/index.php/xarlinkme/list.html?startnum=21
 */

/**
 * return the path for a short URL to xarModURL for this module
 * 
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function xarlinkme_userapi_encode_shorturl($args)
{ 
    /* Get arguments from argument array */
    extract($args);

    /* Check if we have something to work with */
    if (!isset($func)) {
        return;
    }
    
    /* Check if we have module alias set or not */
    $aliasisset = xarModGetVar('xarlinkme', 'useModuleAlias');
    $aliasname = xarModGetVar('xarlinkme','aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }

     $path = '';

    /* if we want to add some common arguments as URL parameters below */
    $join = '?';

    /* we can't rely on xarModGetName() here -> you must specify the modname ! */
    $module = 'xarlinkme';
    $alias = xarModGetAlias($module);

    if ($func == 'main') {
        if (($module == $alias) && ($usealias)){
            $path = '/' . $aliasname . '/';
        } else {
            $path = '/' . $module . '/';
        }
        /* Note : if your main function calls some other function by default,
         * you should set the path to directly to that other function
         */
    } elseif ($func == 'view') {
      if (($module == $alias) && ($usealias)){
            $path = '/' . $aliasname . '/list.html';
        } else {
            $path = '/' . $module . '/list.html';
        }

    } elseif ($func == 'display') {
         /* check for required parameters */
        if (isset($exid) && is_numeric($exid)) {
            if (($module == $alias) && ($usealias)){
                $path = '/' . $aliasname . '/'. $exid . '.html';
            } else {
                $path = '/' . $module . '/' . $exid . '.html';
            }
        } else {

        }
    } else {
        /* anything else that you haven't defined a short URL equivalent for
         *  -> don't create a path here
         */
    }

    /* add some other module arguments as standard URL parameters */
    if (!empty($path)) {
        if (isset($startnum)) {
            $path .= $join . 'startnum=' . $startnum;
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