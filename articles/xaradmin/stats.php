<?php

/**
 * view statistics
 */
function articles_admin_stats()
{
    if (!xarSecurityCheck('AdminArticles')) return;

    $data = array();

    $data['stats'] = xarModAPIFunc('articles','admin','getstats');
    $data['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');
    $data['statuslist'] = xarModAPIFunc('articles','user','getstates');

    return $data;
}

?>
