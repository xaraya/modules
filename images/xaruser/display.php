<?php

/**
 *  Pushes an image to the client browser
 * 
 *  @author   Carl P. Corliss
 *  @access   public
 *  @param    string    fileId          The id (from the uploads module) of the image to push
 *  @returns  boolean                   This function will exit upon succes and, returns False and throws an exception otherwise
 *  @throws   BAD_PARAM                 missing or invalid parameter
 */
 
function images_user_display( $args ) {
    
    extract ($args);
    
    if (!xarVarFetch('fileId', 'int:1:', $fileId)) return;
    if (!xarVarFetch('width',  'str:1:', $width,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('height', 'str:1:', $height, '', XARVAR_NOT_REQUIRED)) return;
 
    $image = xarModAPIFunc('images', 'user', 'load_image', array('fileId' => $fileId));
    if (!is_object($image)) {
        $msg = xarML('Unable to load image : [#(1)]', $fileLocation);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_MISSING', new SystemException($msg));
        return FALSE;
    }
    $fileType = $image->mime;
    $fileName = $image->fileName;

    if (isset($width) && !empty($width)) {
        $width = urldecode($width);
        eregi('([0-9]+)(px|%)', $width, $parts);
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
        eregi('([0-9]+)(px|%)', $height, $parts);
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
        $msg = xarML('Unable to find file: [#(1)]', $fileLocation);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_MISSING', new SystemException($msg));
        return FALSE;
    }
    
    // Close the buffer, saving it's current contents for possible future use
    // then restart the buffer to store the file
    while (ob_get_level()) {
        $pageBuffer[ob_get_level() - 1] = ob_get_contents();
        ob_end_clean();    
    }
    
    // Start buffering for the file
    ob_start();

    $fp = @fopen($fileLocation, 'rb');
    if(is_resource($fp))   {

        do {
            $data = fread($fp, 65536);
            if (strlen($data) == 0) {
                break;
            } else {
                print("$data");
            }
        } while (TRUE);

        fclose($fp);
    } 
    
    // Headers -can- be sent after the actual data 
    // Why do it this way? So we can capture any errors and return if need be :)
    // not that we would have any errors to catch at this point but, mine as well
    // do it incase I think of some errors to catch 
    header("Pragma: ");
    header("Cache-Control: ");
    header("Content-type: $fileType"); 
    header("Content-disposition: inline; filename=\"$fileName\"");
    
    // TODO: evaluate registering shutdown functions to take care of 
    //       ending Xaraya in a safe manner 
    exit();   
}

?>   
