<?php
function netquery_netquickblock_init()
{
    return true;
}
function netquery_netquickblock_info()
{
    return array('text_type' => 'netquick',
                 'module' => 'netquery',
                 'text_type_long' => 'Show Netquick Query Options',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}
function netquery_netquickblock_display($blockinfo)
{
    if (!xarSecurityCheck('OverviewNetquery')) {
        return;
    }
    if (empty($blockinfo['content'])) {
        return '';
    }
    $vars = @unserialize($blockinfo['content']);
    if (!isset($vars['blockquery'])) {
        $vars['blockquery'] = 'whois';
    }
    $httpurl = 'http://'.$_SERVER['SERVER_NAME'];
    $host = $_SERVER['REMOTE_ADDR'];
    $email = 'someone@'.gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $links = array();
    $links = xarModAPIFunc('netquery','user','getlinks');
    $blockinfo['content'] = array('links'   => $links,
                                  'host'    => $host,
                                  'email'   => $email,
                                  'httpurl' => $httpurl,
                                  'vars'    => $vars);
    return $blockinfo;
}
?>