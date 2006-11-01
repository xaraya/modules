<?php
function netquery_userapi_bb2_load()
{
    $settings = xarModAPIFunc('netquery', 'user', 'bb2_settings');
    if (!defined('NQ4_CWD')) define('NQ4_CWD', substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR)));
    if (!defined('BB2_CWD')) define('BB2_CWD', NQ4_CWD);
    require_once(BB2_CWD . "/xarincludes/spamblocker/version.inc.php");
    require_once(BB2_CWD . "/xarincludes/spamblocker/core.inc.php");
    return bb2_start($settings);
}
?>