<?php
function weather_init()
{
    xarModSetVar('weather','partner_id','');
    xarModSetVar('weather','license_key','');
    xarModSetVar('weather','default_location','');
    xarModSetVar('weather','cc_cache_time',60*30); // 30 minutes - these should not be changed
    xarModSetVar('weather','ext_cache_time',60*60*2); // 2 hours - these should not be changed
    xarModSetVar('weather','units','s');
    xarModSetVar('weather','extdays',10);
    
    // Let's register our block
    if (!xarModAPIFunc('blocks','admin','register_block_type',
            array('modName' => 'weather',
                'blockType' => 'current'))) return;
    
    
    // let's define our instances
    $xartable =& xarDBGetTables();
    $query = "SELECT DISTINCT i.xar_title 
              FROM $xartable[block_instances] i, $xartable[block_types] t 
              WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'weather'";
    $instances = array(
        array(
            'header' => 'Weather Block Title:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('weather', 'Block', $instances);
    
    // let's define our masks
    xarRegisterMask('ReadWeatherBlock', 'All', 'weather', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewWeather', 'All', 'weather', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadWeather', 'All', 'weather', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('AdminWeather', 'All', 'wethear', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    return true;
}

function weather_upgrade()
{
    return true;
}

function weather_delete()
{
    xarModDelAllVars('weather');
    // Remove Masks and Instances
    xarRemoveMasks('weather');
    xarRemoveInstances('weather');
    return true;
}

?>
