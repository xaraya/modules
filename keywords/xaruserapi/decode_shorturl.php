<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author mikespub
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
function keywords_userapi_decode_shorturl($params)
{ 
    // Initialise the argument list we will return
    $args = array(); 

    $module = 'keywords';

    if (!empty($params[1])) {
        $args['keyword'] = urldecode($params[1]);
        if (!empty($params[2]) && is_numeric($params[2])) {
            $args['id'] = $params[2];
        }
    }
    return array('main',$args);
    // default : return nothing -> no short URL decoded
} 

?>
