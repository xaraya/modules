<?php

/**
 * Retrieves the name of the image file to use for a given mimetype.
 * If no image file exists for the given mimtype, the unknown image file
 * will be used.
 * 
 * @author  Carl P. Corliss
 * @access  public
 * @param   string mimeType    The mime type to find an correlating image for
 * @returns string
 */

function mime_userapi_get_mime_image( $args ) 
{

    extract ( $args );

     if (!isset($mimeType)) {
        $msg = xarML('Missing parameter [#(1)] for API function [#(2)] in module [#(3)].',
                     'mimeType', 'get_mime_image', 'mime');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }

    $mimeType = explode('/', $mimeType);
    $imageFile = $mimeType[0] . '-' . $mimeType[1] . '.png';
    $imageURI = xarTplGetImage($imageFile, 'mime');

    // try for the complete mimetype-subtype image
    if ($imageURI != NULL) {
        return $imageURI;
    } else {
        // otherwise, use the top level mimtype image
        $imageFile = $mimeType[0] . '.png';
        $imageURI = xarTplGetImage($imageFile, 'mime');
        
        if ($imageURI != NULL) {
            return $imageURI;
        } else {
            return xarTplGetImage('default.png', 'mime');
        }
    }
    
    // Should NEVER get here.
    return FALSE;
}    

?>