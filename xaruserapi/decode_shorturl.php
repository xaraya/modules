<?php
/**
 * Extract function and arguments from short URLS
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Contact Form Module
 * @link http://xaraya.com/index.php/release/1049.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author the Example module development team
 * @param $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function contactform_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = array();

    // Analyse the different parts of the virtual path
    // $params[1] contains the first part after index.php/contactform

    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('new', $args);

    } elseif ($params[1] == 'new') {

        if(isset($params[2])) {
            $args['name'] = $params[2];
        }

        return array('new', $args);

    } elseif ($params[1] == 'success') {

        if(isset($params[2])) {
            $args['name'] = $params[2];
        }

        return array('success', $args);

    } else {
    }

    // default : return nothing -> no short URL decoded
}

?>