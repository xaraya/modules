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
function xlink_userapi_decode_shorturl($params)
{ 
    // Initialise the argument list we will return
    $args = array(); 

    $module = 'xlink';

    // Check if we're dealing with an alias here
    if ($params[0] != $module) {
        $alias = xarModGetAlias($params[0]);
        if ($module == $alias) {
            // yup, looks like it
            $args['base'] = $params[0];
        }
    }
    if (!empty($params[1])) {
        if (!empty($args['base']) || empty($params[2])) {
            $args['id'] = $params[1];
        } else {
            $args['base'] = $params[1];
            $args['id'] = $params[2];
        }
        return array('main',$args);
    }
    // default : return nothing -> no short URL decoded
} 

?>
