<?php

function moveabletype_userapi_publishPost($args)
{
    // We dont need this in Xaraya, so just return success
    return xarModAPIFunc('xmlrpcserver','user','successresponse');
}
?>
