<?php
/**
 * Encode module parameters for Short URL support
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
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
 * - http://mysite.com/index.php/legis/ (main function)
 * - http://mysite.com/index.php/legis/list.html (view function)
 * - http://mysite.com/index.php/legis/123.html (display function)
 *
 * in addition to the 'normal' Xaraya URLs that look like :
 *
 * - http://mysite.com/index.php?module=legis&func=display&exid=123
 *
 * You can also combine the two, e.g. for less frequently-used parameters :
 *
 * - http://mysite.com/index.php/legis/list.html?startnum=21
 */

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the legis module development team 
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function legis_userapi_encode_shorturl($args)
{ 
    /* Get arguments from argument array */
    extract($args);

    /* Check if we have something to work with */
    if (!isset($func)) {
        return;
    }
    
    /* Check if we have module alias set or not */
    $aliasisset = xarModGetVar('legis', 'useModuleAlias');
    $aliasname = xarModGetVar('legis','aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }

    /* Note : make sure you don't pass the following variables as arguments in
     * your module too - adapt here if necessary
     * default path is empty -> no short URL
     */
    $path = '';

    /* if we want to add some common arguments as URL parameters below */
    $join = '?';
    
    /* we can't rely on xarModGetName() here -> you must specify the modname ! */
    $module = 'legis';
    $alias = xarModGetAlias($module);
    /* specify some short URLs relevant to your module */
    if ($func == 'main') {
        if (isset($reset) && ($reset ==1)) {
          if (($module == $alias) && ($usealias)){
                $path = '/' . $aliasname . '/reset';
            } else {
                $path = '/' . $module . '/reset';
            }
       }elseif (isset($defaulthall) && is_numeric($defaulthall)) {
          if (($module == $alias) && ($usealias)){
                $path = '/' . $aliasname . '/sethall/'.$defaulthall;
            } else {
                $path = '/' . $module . '/sethall/'.$defaulthall;
            }
        } else {
            if (($module == $alias) && ($usealias)){
                $path = '/' . $aliasname . '/';
            } else {
                $path = '/' . $module . '/';
            }
        }

    } elseif ($func == 'view') {
      if (($module == $alias) && ($usealias)){
          if (isset($docstatus) && ($docstatus ==1)) {
            $path = '/' . $aliasname . '/pending/';
          }else {
            $path = '/' . $aliasname . '/view/';
          }
        } else {
          if (isset($docstatus) && ($docstatus ==1)) {
            $path = '/' . $module . '/pending/';
          }else {
            $path = '/' . $module . '/view/';
          }
        }
    } elseif ($func == 'addlegis') {
        if (($module == $alias) && ($usealias)){
            $path = '/' . $aliasname . '/add';
        } else {
            $path = '/' . $module . '/add';
        }
    } elseif ($func == 'display') {
         /* check for required parameters */
        if (isset($cdid)) {
            if (($module == $alias) && ($usealias)){
                $path = '/' . $aliasname . '/display/'. $cdid;
            } else {
                $path = '/' . $module . '/display/' . $cdid;
            }
        } else {
            /* we don't know how to handle that -> don't create a path here
             * Note : this generally means that someone tried to create a
             * link to your module, but used invalid parameters for xarModURL
             * -> you might want to provide a default path to return to
             * $path = '/' . $module . '/list.html';
             */
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
