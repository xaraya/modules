<?php
/**
 * File: $Id:
 * 
 * Support for short URLs (user functions)
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarcpshop
 * @author xarCPShop module development team 
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
 * - http://mysite.com/index.php/xarcpshop/ (main function)
 * - http://mysite.com/index.php/xarcpshop/shopid (main function)
 * - http://mysite.com/index.php/xarcpshop/shopid.itemid (main function )
 * - http://mysite.com/index.php/xarcpshop/shopid.itemid?query (main function)
 * in addition to the 'normal' Xaraya URLs that look like :
 *
  *
 */

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author jojodee
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function xarcpshop_userapi_encode_shorturl($args)
{ 
    // Get arguments from argument array
    extract($args);
    //var_dump($args);
    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

    $path = ''; 
    // if we want to add some common arguments as URL parameters below
    $join = '?'; 
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'xarcpshop';
    // specify some short URLs relevant to your module
    if ($func == 'main') {
        $path = '/' . $module . '/'; 
        // Note : if your main function calls some other function by default,
        // you should set the path to directly to that other function
    } elseif ($func == 'view') {
        $path = '/' . $module . '/list.html'; 

    } elseif ($func == 'display') {
        // check for required parameters
        if (isset($id) && is_numeric($id)) {
            $path = '/' . $module . '/' . $id ;
            // you might have some additional parameter that you want to use to
            // create different virtual paths here - for xarcpshop an item is in the $id
        } else {

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
