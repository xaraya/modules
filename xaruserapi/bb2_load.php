<?php
function netquery_userapi_bb2_load()
{
    $settings = xarModAPIFunc('netquery', 'user', 'bb2_settings');
    $pdir = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR));
    define('BB2_CWD', $pdir);
    require_once(BB2_CWD . "/xarincludes/spamblocker/core.inc.php");
    return bb2_start($settings);
}
?>