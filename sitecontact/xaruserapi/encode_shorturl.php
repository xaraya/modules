<?php
/**
 * File: $Id
 * 
 * Support for short URLs (user functions)
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Site Contact
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/*
 * Support for short URLs (user functions)
 *

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
    // Get arguments from argument array
    extract($args); 
    // Check if we have something to work with
    if (!isset($func)) {
        return;
    } 
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'sitecontact';
    // specify some short URLs relevant to your module
    if ($func == 'main') {
        $path = '/' . $module . '/';
    } elseif ($func == 'contactus') {
        $path = '/' . $module . '/contactus';
        if (isset($message) && is_numeric($message)) {
            $path = '/' . $module . '/contactus/' . $message;
        } else {
        }
    } else {
    }
    // add some other module arguments as standard URL parameters
    if (!empty($path)) {
        if (isset($startnum)) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&amp;';
        }
        if (!empty($catid)) {
            $path .= $join . 'catid=' . $catid;
            $join = '&amp;';
        } elseif (!empty($cids) && count($cids) > 0) {
            if (!empty($andcids)) {
                $catid = join('+', $cids);
            } else {
                $catid = join('-', $cids);
            }
            $path .= $join . 'catid=' . $catid;
            $join = '&amp;';
        }
    }

    return $path;
}

?>
