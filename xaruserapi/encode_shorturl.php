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
 * @subpackage bible
 * @author curtisdf
 */
/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author curtisdf
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function bible_userapi_encode_shorturl($args)
{
    extract($args);

    if (!isset($func)) return;

    // use module alias if set
    $usemodulealias = xarModGetVar('bible', 'useModuleAlias');
    $aliasname = xarModGetVar('bible', 'aliasname');
    $module = ($usemodulealias) ? $aliasname : 'bible';

    $path = '';
    $join = '?';

    // specify short URLs
    if (isset($sname) &&
       ($func == 'main' || $func == 'view' || $func == 'display' || $func == 'dictionary')) {
        $path = "/$module/$sname/";
        if (isset($query)) {
            $path .= "$query/";
        }
    } elseif ($func == 'main') {
        $path = "/$module/";
    } elseif ($func == 'query') {
        $path = "/$module/query/";
    } elseif ($func == 'search') {
        $path = "/$module/search/";
    } elseif ($func == 'lookup') {
        $path = "/$module/lookup/";
    } elseif ($func == 'dictionary') {
        $path = "/$module/dictionary/";
    } elseif ($func == 'library') {
        $path = "/$module/library/";
        if (isset($sname)) $path .= "$sname/";
    } elseif ($func == 'help') {
        $path = "/$module/help/";
    }
    // add some other module arguments as standard URL parameters
    if (!empty($path)) {
        if (isset($startnum)) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&';
        }
        if (isset($showcontext) && $showcontext) {
            $path .= $join . 'showcontext=1';
            $join = '&';
        }
        if (isset($numitems)) {
            $path .= $join . 'numitems=' . $numitems;
            $join = '&';
        }
        if (!empty($lastlimits)) {
            $path .= $join . 'lastlimits=' . $lastlimits;
            $join = '&';
        }
        if (!empty($string)) {
            $path .= $join . 'string=' . urlencode($string);
            $join = '&';
        }
    }

    return $path;
}

?>
