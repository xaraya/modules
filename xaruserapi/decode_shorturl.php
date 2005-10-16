<?php
/**
 * Extract function and arguments from short URLs
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

/**
 * Extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 * 
 * @param  $params array containing the different elements of the virtual path
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function xarlinkme_userapi_decode_shorturl($params)
{ 
    /* Initialise the argument list we will return */
    $args = array();
    $module = 'xarlinkme';
    /* Check and see if we have a module alias */
    $aliasisset = xarModGetVar('xarlinkme', 'useModuleAlias');
    $aliasname = xarModGetVar('xarlinkme','aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }

    if ($params[0] != $module) { /* it's possibly some type of alias */
        $aliasname = xarModGetVar('xarlinkme','aliasname');
    }
    if (empty($params[1])) {
        /*( nothing specified -> we'll go to the main function */
        return array('main', $args);
    } elseif (preg_match('/^index/i', $params[1])) {
        /* some search engine/someone tried using index.html (or similar)
         * -> we'll go to the main function
         */
        return array('main', $args);
    } elseif (preg_match('/^list/i', $params[1])) {
        /* something that starts with 'list' is probably for the view function
         * Note : make sure your encoding/decoding is consistent ! :-)
         */
        return array('view', $args);
    } elseif (preg_match('/^(\d+)/', $params[1], $matches)) {
        /* something that starts with a number must be for the display function
         * Note : make sure your encoding/decoding is consistent ! :-)
         */
        $bnid = $matches[1];
        $args['exid'] = $bnid;
        return array('display', $args);
    } else {

    }
    /* default : return nothing -> no short URL decoded */
} 
?>