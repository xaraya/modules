<?php
function sitecontact_adminapi_getconfighook($args)
{
    extract($args);
    if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
    $module = 'sitecontact';
    $tabinfo = array(
            'module'  => $module,
            'configarea'  => 'general',
            'configtitle'  => xarML('Site Contact'),
            'configcontent' => ''
    );
    $extrainfo['tabs'][] = $tabinfo;
    return $extrainfo;
}
?>