<?php
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
function headlines_userapi_decode_shorturl($params)
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
    } elseif (preg_match('/^(\d+)/',$params[1],$matches)) {
        // something that starts with a number must be for the display function
        // Note : make sure your encoding/decoding is consistent ! :-)
        $hid = $matches[1];
        $args['hid'] = $hid;
        return array('view', $args);
    }
    // default : return nothing -> no short URL decoded
} 

?>