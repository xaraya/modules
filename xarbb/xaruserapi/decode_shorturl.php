<?php
/**
 * File: $Id$
 * 
 * Standard function to decode short urls
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 * 
 * @author the xarBB module development team 
 * @param  $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function xarbb_userapi_decode_shorturl($params)
{ 
    // Initialise the argument list we will return
    $args = array(); 
    // Analyse the different parts of the virtual path
    // $params[1] contains the first part after index.php/xarbb
    // In general, you should be strict in encoding URLs, but as liberal
    // as possible in trying to decode them...
    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('main', $args);
    } elseif (preg_match('/^index/i', $params[1])) {
        // some search engine/someone tried using index.html (or similar)
        // -> we'll go to the main function
        return array('main', $args);
    } elseif (preg_match('/^forum/i', $params[1])) {
        // something that starts with 'list' is probably for the view function
        // Note : make sure your encoding/decoding is consistent ! :-)
        if (preg_match('/^(\d+)/', $params[2], $matches)) {
           $fid = $matches[1];
           $args['fid'] = $fid;
        }
        return array('viewforum', $args);
    } elseif (preg_match('/^topic/i', $params[1])) {
        // something that starts with 'list' is probably for the view function
        // Note : make sure your encoding/decoding is consistent ! :-)
        if (preg_match('/^(\d+)/', $params[2], $matches)) {
           $tid = $matches[1];
           $args['tid'] = $tid;
        }
        return array('viewtopic', $args);
    } else {
        // the first part might be something variable like a category name
        // In order to match that, you'll have to retrieve all relevant
        // categories for this module, and compare against them...
        // $cid = xarModGetVar('xarbb','mastercids');
        // if (xarModAPILoad('categories','user')) {
        // $cats = xarModAPIFunc('categories',
        // 'user',
        // 'getcat',
        // array('cid' => $cid,
        // 'return_itself' => true,
        // 'getchildren' => true));
        // // lower-case for fanciful search engines/people
        // $params[1] = strtolower($params[1]);
        // $foundcid = 0;
        // foreach ($cats as $cat) {
        // if ($params[1] == strtolower($cat['name'])) {
        // $foundcid = $cat['cid'];
        // break;
        // }
        // }
        // // check if we found a matching category
        // if (!empty($foundcid)) {
        // $args['cid'] = $foundcid;
        // // TODO: now analyse $params[2] for index, list, \d+ etc.
        // // and return array('whatever', $args);
        // }
        // }
        // we have no idea what this virtual path could be, so we'll just
        // forget about trying to decode this thing
        // you *could* return the main function here if you want to
        // return array('main', $args);
    } 
    // default : return nothing -> no short URL decoded
} 

?>
