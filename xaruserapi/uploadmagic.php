<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */function uploads_userapi_uploadmagic($args)
{
    $fileUpload = xarModAPIFunc('uploads','user','upload',$args);

    if( is_array($fileUpload) )
    {
        return '#file:' . $fileUpload['ulid'] . '#';

    } else {
        return $fileUpload;
    }
}
?>
