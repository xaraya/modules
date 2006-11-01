<?php
function netquery_nqmonitorblock_init()
{
    return array(
        'nocache'     => 0,
        'pageshared'  => 1,
        'usershared'  => 1,
        'cacheexpire' => null
    );
}
function netquery_nqmonitorblock_info()
{
    return array(
        'text_type' => 'nqmonitor',
        'module' => 'netquery',
        'text_type_long' => xarML('Netquery Access Monitor'),
        'allow_multiple' => false,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}
function netquery_nqmonitorblock_display($blockinfo)
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