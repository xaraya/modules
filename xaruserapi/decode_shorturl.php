<?php
/*
 *
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author the Example module development team
 * @param  $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function polls_userapi_decode_shorturl($params)
{
    $args = array();
    if (empty($params[1])) {
        return array('main', $args);
    } elseif (preg_match('/^index/i', $params[1])) {
        return array('main', $args);
    } elseif (preg_match('/^results/', $params[1])) {
        $pid = $params[2];
        $args['pid'] = $pid;
        return array('results', $args);
    } elseif (preg_match('/^vote/', $params[1])) {
        $pid = $params[2];
        $args['pid'] = $pid;
        return array('display', $args);
    } else {
    }
}

?>