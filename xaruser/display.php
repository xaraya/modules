<?php
/**
 * Images Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 *  Pushes an image to the client browser
 *
 *  @author   Carl P. Corliss
 *  @access   public
 *  @param    string    fileId          The id (from the uploads module) of the image to push
 *  @return   boolean                   This function will exit upon succes and, returns False and throws an exception otherwise
 *  @throws   BAD_PARAM                 missing or invalid parameter
 */
function images_user_display( $args )
{

    extract ($args);

    if (!xarVarFetch('fileId', 'str:1:', $fileId)) return;
    if (!xarVarFetch('width',  'str:1:', $width,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('height', 'str:1:', $height, '', XARVAR_NOT_REQUIRED)) return;

    if (is_numeric($fileId)) {
        $data = array('fileId' => $fileId);
    } else {
        $fileLocation = base64_decode($fileId);
        if (empty($fileLocation) || !file_exists($fileLocation)) {
            return FALSE;
        }
        $data = array('fileLocation' => $fileLocation);
    }

    $image = xarModAPIFunc('images', 'user', 'load_image', $data);

    if (!is_object($image)) {
        xarResponse::Redirect('modules/images/xarimages/admin.gif');
        return TRUE;
    //    $msg = xarML('Unable to find file: [#(1)]', $fileId);
    //    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_MISSING', new SystemException($msg));
    //    return FALSE;
    }

    $fileType =& $image->mime;
    $fileName =& $image->fileName;

    if (isset($width) && !empty($width)) {
        $width = urldecode($width);
        eregi('([0-9]+)(px|%)?', $width, $parts);
        $type = ($parts[2] == '%') ? _IMAGES_UNIT_TYPE_PERCENT : _IMAGES_UNIT_TYPE_PIXELS;
        switch ($type) {
            case _IMAGES_UNIT_TYPE_PERCENT:
                $image->setPercent(array('wpercent' => $width));
                break;
            default:
            case _IMAGES_UNIT_TYPE_PIXELS:
                $image->setWidth($parts[1]);

        }

        if (!isset($height) || empty($height)) {
            $image->Constrain('width');
        }
    }

    if (isset($height) && !empty($height)) {
        $height = urldecode($height);
        eregi('([0-9]+)(px|%)?', $height, $parts);
        $type = ($parts[2] == '%') ? _IMAGES_UNIT_TYPE_PERCENT : _IMAGES_UNIT_TYPE_PIXELS;
        switch ($type) {
            case _IMAGES_UNIT_TYPE_PERCENT:
                $image->setPercent(array('hpercent' => $height));
                break;
            default:
            case _IMAGES_UNIT_TYPE_PIXELS:
                $image->setHeight($parts[1]);

        }

        if (!isset($width) || empty($width)) {
            $image->Constrain('height');
        }
    }

    $fileLocation = $image->getDerivative();

    if (is_null($fileLocation)) {
        $msg = xarML('Unable to find file: [#(1)]', $fileId);
        throw new Exception($msg);
    }

    // Close the buffer, saving it's current contents for possible future use
    // then restart the buffer to store the file
    $pageBuffer = xarModAPIFunc('base', 'user', 'get_output_buffer');

    ob_start();

    if (file_exists($fileLocation)) {
        $fileSize = @filesize($fileLocation);
        if (empty($fileSize)) {
            $fileSize = 0;
        }

        $fp = @fopen($fileLocation, 'rb');
        if(is_resource($fp))   {

            do {
                $data = fread($fp, 65536);
                if (strlen($data) == 0) {
                    break;
                } else {
                    echo "$data";
                }
            } while (TRUE);

            fclose($fp);
        }

// FIXME: make sure the file is indeed supposed to be stored in the database :-)
    } elseif (is_numeric($fileId) && xarModIsAvailable('uploads')) {
        $fileSize = 0;

        // get the image data from the database
        $data = xarModAPIFunc('uploads', 'user', 'db_get_file_data', array('fileId' => $fileId));
        if (!empty($data)) {
            foreach ($data as $chunk) {
                $fileSize += strlen($chunk);
                echo $chunk;
            }
            unset($data);
        }

    } else {
        xarResponse::Redirect('modules/images/xarimages/admin.gif');
        return TRUE;
    }

    // Headers -can- be sent after the actual data
    // Why do it this way? So we can capture any errors and return if need be :)
    // not that we would have any errors to catch at this point but, mine as well
    // do it incase I think of some errors to catch

    // Make sure to check the browser / os type - IE 5.x on Mac (os9 / osX / etc) does
    // not like headers being sent for iamges - so leave them out for those particular cases
    $osName      = xarSession::getVar('osname');
    $browserName = xarSession::getVar('browsername');

    if (empty($osName) || $osName != 'mac' || ($osName == 'mac' && !stristr($browserName, 'internet explorer'))) {
        header("Pragma: ");
        header("Cache-Control: ");
        header("Content-type: $fileType[text]");
        header("Content-disposition: inline; filename=\"$fileName\"");

        if ($fileSize) {
            header("Content-length: $fileSize");
        }
    }
    // TODO: evaluate registering shutdown functions to take care of
    //       ending Xaraya in a safe manner
    exit();
}

?>
