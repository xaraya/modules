<?php
function calendar_adminapi_getconfighook($args)
{
    extract($args);
    if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
    $module = 'quotas';
    $tabinfo = array(
            'module'  => $module,
            'configarea'  => 'general',
            'configtitle'  => xarML('Calendar'),
            'configcontent' => ''
    );
    $extrainfo['tabs'][] = $tabinfo;
    return $extrainfo;
}
?>