<?php
/**
 * File: $Id:
 * 
 * Extract function and arguments from short URLs for sitecontact
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage sitecontact
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 * 
 * @author Jo Dalle Nogare
 * @param  $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function sitecontact_userapi_decode_shorturl($params)
{ 
    // Initialise the argument list we will return
    $args = array(); 
    // Analyse the different parts of the virtual path
    // $params[1] contains the first part after index.php/example
    // In general, you should be strict in encoding URLs, but as liberal
    // as possible in trying to decode them...
    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('main', $args);
    } elseif (preg_match('/^index/i', $params[1])) {
        // some search engine/someone tried using index.html (or similar)
        // -> we'll go to the main function
        return array('main', $args);
    } elseif (preg_match('/^contactus/i', $params[1])) {
        // something that starts with 'list' is probably for the view function
        // Note : make sure your encoding/decoding is consistent ! :-)
        return array('contactus', $args);
    } elseif (preg_match('/^(\d+)/', $params[1], $matches)) {
         $messageid = $matches[1];
        $args['message'] = $messageid;
        return array('main', $args);
    } else {

    } 

} 

?>
