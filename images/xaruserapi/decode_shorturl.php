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
function images_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = array();

    // Analyse the different parts of the virtual path
    // $params[1] contains the first part after index.php/example

    // In general, you should be strict in encoding URLs, but as liberal
    // as possible in trying to decode them...
    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('display', $args);

    } elseif (preg_match('/^(\d+)\.(.*)/', $params[1], $matches)) {

        // something that starts with a number must be for the display function
        // Note : make sure your encoding/decoding is consistent ! :-)
        $fileId = $matches[1];
        $type   = $matches[2];

        // jpg is actually jpeg mime subtype - use jpeg spelling for type check
        if ($type == 'jpg')  {
            $type = 'jpeg';
        }

        $fileInfo = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileId));

        if (!isset($fileInfo[$fileId]) || !count($fileInfo[$fileId])) {
            $msg = xarML('Unable to display - file \'#(1)\' does not exist!', $params[1] );
            xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NO_EXIST', new DefaultUserException($msg));
            return;
        } else {
            $fileInfo = $fileInfo[$fileId];
            $mimeType = explode('/', $fileInfo['fileType']);

            // ensure that the jpeg mime subtype is 'jpeg'
            if ($mimeType[1] == 'jpg') {
                $mimeType[1] = 'jpeg';
            }

            if ($mimeType[0] != 'image' || $mimeType[1] != $type) {

                $msg = xarML('Unable to display. File \'#(1)\' is either not an image or does not exist.', $params[1]);
                xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NO_EXIST', new DefaultUserException($msg));
                return;
            } else {

                $args['fileId'] = $fileId;
                return array('display', $args);
            }
        }
    }

}

?>