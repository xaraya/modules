<?php
/**
 * Encode short urls
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitecontact
 */

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author Jo Dalle Nogare
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function sitecontact_userapi_encode_shorturl($args)
{ 
    /* Get arguments from argument array */
    extract($args);
    /* Check if we have something to work with */
    if (!isset($func)) {
        return;
    } 
    
    $aliasisset = xarModGetVar('sitecontact', 'useModuleAlias');
    $aliasname = xarModGetVar('sitecontact','aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }
    $path = '';
    /* if we want to add some common arguments as URL parameters below */
    $join = '?';
    /* we can't rely on xarModGetName() here -> you must specify the modname */
    $module = 'sitecontact';
    $alias = xarModGetAlias($module);
    /* specify some short URLs relevant to your module */
    if ($func == 'main') {
        if (($module == $alias) && ($usealias)){
            /* OK, we can use a 'fake' module name here */
            $path = '/' . $aliasname . '/';
            if (isset($message) && is_numeric($message)) {
                $path = '/' . $aliasname . '/' . $message;
            }
        }else {
            $path = '/' . $module . '/';
            if (isset($message) && is_numeric($message)) {
                $path = '/' . $module . '/' . $message;
            }
        }
    } elseif ($func == 'contactus') {
          if (($module == $alias) && ($usealias)){
              $path = '/' . $aliasname . '/contactus';
              if (isset($message) && is_numeric($message)) {
                  $path = '/' .$aliasname  . '/contactus/' . $message;
              }
          }else{
              $path = '/' . $module . '/contactus';
              if (isset($message) && is_numeric($message)) {
                  $path = '/' . $module . '/contactus/' . $message;
              }
          }
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