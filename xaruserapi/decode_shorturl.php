<?php
/**
 * Extract function and arguments from short URLS
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author mikespub <mikespub@xaraya.com>
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
function dossier_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = array();

    // Analyse the different parts of the virtual path
    // $params[1] contains the first part after index.php/dossier

    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('main', $args);

    } elseif (preg_match('/^index/i',$params[1])) {
        // some search engine/someone tried using index.html (or similar)
        // -> we'll go to the main function
        return array('main', $args);

    } elseif (preg_match('/^list/i',$params[1])) {
        // something that starts with 'list' is probably for the view function
        // Note : make sure your encoding/decoding is consistent ! :-)
        return array('view', $args);

    } elseif (preg_match('/^(\d+)/',$params[1],$matches)) {
        // something that starts with a number must be for the display function
        // Note : make sure your encoding/decoding is consistent ! :-)
        $contactid = $matches[1];
        $args['contactid'] = $contactid;
        return array('display', $args);

    } else {
    }

    // default : return nothing -> no short URL decoded
}

?>
