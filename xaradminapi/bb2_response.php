<?php
function netquery_adminapi_bb2_response($args)
{
    extract($args);
    if (!isset($key))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $response = array('response' => 0, 'explanation' => '', 'log' => '');
    $pdir = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR));
    if (!defined('BB2_CORE')) define('BB2_CORE', $pdir.'/xarincludes/spamblocker');
    include_once(BB2_CORE . '/responses.inc.php');
    if (is_callable('bb2_get_response')) $response = bb2_get_response($key);
    if ($response['response'] == '200')
    {
        $response['explanation'] = 'No problem detected';
        $response['log'] = 'Request accepted';
    }
    return $response;
}
?>
