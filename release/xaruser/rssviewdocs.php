<?php

function release_user_rssviewdocs()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // The user API function is called. 
    $id = xarModAPIFunc('release',
                         'user',
                         'getallids',
                          array('certified' => '2'));


}

?>