<?php

/**
 * Get information about an image (from file or database)
 *
 * @param   integer $fileId        The (uploads) file id of the image, or
 * @param   string  $fileLocation  The file location of the image
 * @param   string  $basedir       (optional) The directory where images are stored
 * @param   string  $baseurl       (optional) The corresponding base URL for the images
 * @returns array
 * @return an array containing the image information if available
 */
function images_userapi_getimageinfo( $args ) 
{
    extract($args);

    if (empty($fileId) && empty($fileLocation)) {
        $mesg = xarML('Invalid parameter \'#(1)\' to API function \'#(2)\' in module \'#(3)\'', 
                      '', 'getimageinfo', 'images');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($mesg));
        return;
    } elseif (!empty($fileId) && !is_numeric($fileId)) {
        $mesg = xarML('Invalid parameter \'#(1)\' to API function \'#(2)\' in module \'#(3)\'', 
                      'fileId', 'getimageinfo', 'images');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($mesg));
        return;
    } elseif (!empty($fileLocation) && !is_string($fileLocation)) {
        $mesg = xarML('Invalid parameter \'#(1)\' to API function \'#(2)\' in module \'#(3)\'', 
                      'fileLocation', 'getimageinfo', 'images');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($mesg));
        return;
    }

    if (!empty($fileId) && is_numeric($fileId)) {
        // Get file information from the uploads module
        $imageInfo = end(xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileId)));

        if (!empty($imageInfo)) {
            // Check the modified and writable
            if (file_exists($imageInfo['fileLocation'])) {
                $imageInfo['fileModified'] = @filemtime($imageInfo['fileLocation']);
                $imageInfo['isWritable']   = @is_writable($imageInfo['fileLocation']);
            } else {
                $imageInfo['fileModified'] = '';
                $imageInfo['isWritable']   = false;
            }
            // Get image size and type information
            $sizeinfo = xarModAPIFunc('images','user','getimagesize',$imageInfo);
            if (!empty($sizeinfo)) {
                $imageInfo['imageWidth']  = $sizeinfo[0];
                $imageInfo['imageHeight'] = $sizeinfo[1];
                $imageInfo['imageType']   = $sizeinfo[2];
                $imageInfo['imageAttr']   = $sizeinfo[3];
            }
        }
        return $imageInfo;

    } elseif (!empty($fileLocation)) {
        // Check if the file exists
        $fileName = $fileLocation;
        if (!empty($basedir) && file_exists($basedir . '/' . $fileName)) {
            $fileLocation = $basedir . '/' . $fileName;
        } elseif (file_exists($fileName)) {
            $fileLocation = $fileName;
        } else {
            return;
        }
        // Get file statistics
        $statinfo = @stat($fileLocation);
        // Get image size and type information
        $sizeinfo = @getimagesize($fileLocation);
        if (empty($statinfo) || empty($sizeinfo)) return;

        // Note: we're using base 64 encoded fileId's here
        $id = base64_encode($fileName);
        $imageInfo = array('fileLocation' => $fileLocation,
                           'fileDownload' => (!empty($baseurl) ? $baseurl . '/' . $fileName : $fileName),
                           'fileName'     => $fileName,
                           'fileType'     => $sizeinfo['mime'],
                           'fileSize'     => $statinfo['size'],
                           'fileId'       => $id,
                           'fileModified' => $statinfo['mtime'],
                           'isWritable'   => @is_writable($fileLocation),
                           'imageWidth'   => $sizeinfo[0],
                           'imageHeight'  => $sizeinfo[1],
                           'imageType'    => $sizeinfo[2],
                           'imageAttr'    => $sizeinfo[3]);

        return $imageInfo;
    }

    return;
}

?>
