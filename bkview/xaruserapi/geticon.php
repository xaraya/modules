<?php

/**
 * Get an icon url for a certain file in the repository
 *
 * @package modules
 * @copyright (C) 2004 The Digital Development Foundation, Inc.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function bkview_userapi_geticon($args) 
{
    extract($args);
    $icon = '';
           
    if(xarModIsAvailable('mime') && file_exists($file)) {
        $mime_type = xarModAPIFunc('mime','user','analyze_file',array('fileName' => $file));
        $icon = xarModApiFunc('mime','user','get_mime_image',array('mimeType' => $mime_type));
    } else {
        $icon = xarTplGetImage('bkmissing.png','bkview');
    }
    return $icon;
}