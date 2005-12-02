<?php
/**
 * File: $Id:
 *
 * Extract function and arguments from short URLs for this module
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
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author curtisdf
 * @param  $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function bible_userapi_decode_shorturl($params)
{
    $args = array();

    // get rid of ".html";
    $lastkey = count($params) - 1;
    $params[$lastkey] = preg_replace("/(index)?\.html\$/", '', $params[$lastkey]);

    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('main', $args);

    } elseif (preg_match('/^help/i', $params[1])) {
        return array('help', $args);

    } elseif (preg_match('/^concordance/i', $params[1])) {
        return array('concordance', $args);

    } elseif (preg_match('/^query/i', $params[1])) {
        if (isset($params[2])) {
            $args['sname'] = $params[2];
            if (isset($params[3])) $args['query'] = $params[3];
        }
        return array('query', $args);

    } elseif (preg_match('/^library/i', $params[1])) {
        if (isset($params[2])) $args['sname'] = $params[2];
        return array('library', $args);

    } elseif (preg_match('/^search/i', $params[1])) {
        if (isset($params[2])) {
            $args['sname'] = $params[2];
            if (isset($params[3])) $args['query'] = $params[3];
        }
        return array('search', $args);

    } elseif (preg_match('/^lookup/i', $params[1])) {
        if (isset($params[2])) {
            $args['sname'] = $params[2];
            if (isset($params[3])) $args['query'] = $params[3];
        }
        return array('lookup', $args);

    } elseif (preg_match('/^Strongs(Greek|Hebrew)/i', $params[1])) {

        if (!isset($params[2])) {
            $params[2] = '';
        }
        $args['sname'] = $params[1];
        $args['query'] = $params[2];
        return array('strongs', $args);

    } else {

        if (!isset($params[2])) {
            $params[2] = '';
        }

        // figure out what kind of request it is for
        $function = xarModAPIFunc('bible', 'user', 'getquerytype',
                                  array('sname' => $params[1],
                                        'query' => $params[2]));

        $args['sname'] = $params[1];
        $args['query'] = $params[2];

        return array($function, $args);
    }
    // default : return nothing -> no short URL decoded
}

?>
