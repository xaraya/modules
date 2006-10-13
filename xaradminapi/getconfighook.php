<?php
function foo_adminapi_getconfighook($args)
{
    extract($args);
    if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
    $module = 'foo';
    $tabinfo = array(
            'module'  => $module,
            'configarea'  => 'general',
            'configtitle'  => xarML('Foo'),
            'configcontent' => ''
    );
    $extrainfo['tabs'][] = $tabinfo;
    return $extrainfo;
}
?>