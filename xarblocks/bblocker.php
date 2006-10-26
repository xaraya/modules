<?php
function netquery_bblockerblock_init()
{
    return array(
        'nocache'     => 0,
        'pageshared'  => 1,
        'usershared'  => 1,
        'cacheexpire' => null
    );
}
function netquery_bblockerblock_info()
{
    return array(
        'text_type' => 'bblocker',
        'module' => 'netquery',
        'text_type_long' => xarML('Netquery spambot blocker'),
        'allow_multiple' => false,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}
function netquery_bblockerblock_display($blockinfo)
{
    if (!is_array($blockinfo['content']))
    {
        $vars = @unserialize($blockinfo['content']);
    }
    else
    {
        $vars = $blockinfo['content'];
    }
    $data = array();
    $data['bbbsettings'] = xarModAPIFunc('netquery', 'user', 'bb2_settings');
    $data['bbbstats'] = xarModAPIFunc('netquery', 'user', 'bb2_stats');
    $data['bbbstart'] = xarModAPIFunc('netquery', 'user', 'bb2_load');
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>