<?php

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
function uploads_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = array();

    // In general, you should be strict in encoding URLs, but as liberal
    // as possible in trying to decode them...
    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('file_browser', $args);

    }
    $path = $params;

    $module = array_shift($path);

    // check first for a fileId for backwards compatability
    if (preg_match('/^(\d+)\.(.*)$/', end($path), $matches)) {
        // If we have two parts to our match, and the first is
        // numeric and the second a string, then we probably have
        // a match for an old style shorturl
        if ((count($matches) - 1) == 2 && is_numeric($matches[1])) {
            $args['fileId'] = $matches[1];
            return array('download', $args);
        }
    } 
    
    // Assume this is the path to a folder first
    $vdirInfo = xarModAPIfunc('uploads', 'vdir', 'path_decode', array('path' => $path));

    // If we got false back, then we might have a virtual directory path to a file
    if (FALSE === $vdirInfo) {
        $path = '/' . implode('/', $path) . "/$fileName";

        $errMsg = xarML('Xaraya can not find a file or folder by the name of "#(1)." Please check the spelling and try again.', $path);
        $args['error']['message']   = addslashes($errMsg);
        $args['error']['number']    = _UPLOADS_ERROR_INVALID_PATH;

    } elseif(is_array($vdirInfo)) {

        if (isset($vdirInfo['fileId'])) {
            $args['fileId'] = $vdirInfo['fileId'];
            return array('download', $args);

        } else {

            if (is_array($path)) {
                $path = implode('/', $path);
            }
            $args['module']  = $module;
            $args['vpath']   = $path;
            $args['vdir_id'] = $vdirInfo['dirId'];;
        }
    }

    return array('file_browser', $args);

}
?>