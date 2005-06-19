<?php

/**
 * Replaces an image with a resized image to the given dimensions
 *
 * @param   integer $fileId        The (uploads) file id of the image to load, or
 * @param   string  $fileLocation  The file location of the image to load
 * @param   string  $height     The new height (in pixels or percent) ([0-9]+)(px|%)
 * @param   string  $width      The new width (in pixels or percent)  ([0-9]+)(px|%)
 * @param   boolean $constrain  if height XOR width, then constrain the missing value to the given one
 * @returns string
 * @return the location of the newly resized image
 */

function images_adminapi_replace_image($args)
{
    extract($args);

    if (!empty($fileId) && empty($fileLocation)) {
        $fileInfo = end(xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileId)));
        if (empty($fileInfo)) {
            return;
        } else {
            $fileLocation = $fileInfo['fileLocation'];
        }
    }

    // make sure we can replace the file first
    if (file_exists($fileLocation)) {
        $checkwrite = $fileLocation;
    } else {
        $checkwrite = dirname($fileLocation);
    }
    if (!is_writable($checkwrite)) {
        $mesg = xarML('Unable to replace #(1) - please check your file permissions',
                      $fileLocation);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($mesg));
        return;
    }

// TODO: replace files stored in xar_file_data too

    $location = xarModAPIFunc('images','admin','resize_image',
                              array('fileLocation' => $fileLocation,
                                    'width'  => (!empty($width) ? $width : NULL),
                                    'height' => (!empty($height) ? $height : NULL),
                                    'derivName'   => $fileLocation,
                                    'forceResize' => true));
    if (!$location) return;

    if (empty($fileId)) {
        // We're done here
        return $location;
    }

    // Update the uploads database information
    if (!xarModAPIFunc('uploads','user','db_modify_file',
                       array('fileId'   => $fileId,
    // FIXME: resize() always uses JPEG format for now
                             'fileType' => 'image/jpeg',
                             'fileSize' => filesize($fileLocation)))) {
        return;
    }

    return $location;
}

?>
