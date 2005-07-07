<?php
function uploads_userapi_uploadmagic($args) 
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
