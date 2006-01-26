<?php
/**
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the Example module development team
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */

function polls_userapi_encode_shorturl($args)
{
    extract($args);
    if (!isset($func)) {
        return;
    }
    $path = '';
    $join = '?';
    $module = 'polls';
    if ($func == 'main') {
        $path = '/' . $module . '/';
    } elseif ($func == 'list') {
        $path = '/' . $module . '/list';
    } elseif ($func == 'results') {
        if (isset($pid) && is_numeric($pid)) {
            $path = '/' . $module . '/results/' . $pid;
             } else {
        }
    } elseif ($func == 'display') {
        if (isset($pid) && is_numeric($pid)) {
            $path = '/' . $module . '/vote/' . $pid;
        } else {
        }
    } else {
    }
    return $path;
}

?>
