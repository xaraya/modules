<?php
function weather_init()
{
    xarModSetVar('weather','partner_id','');
    xarModSetVar('weather','license_key','');
    xarModSetVar('weather','default_location','');
    xarModSetVar('weather','cache_dir','modules/weather/xarcache');
    xarModSetVar('weather','cc_cache_time',60*30); // 30 minutes - these should not be changed
    xarModSetVar('weather','ext_cache_time',60*60*2); // 2 hours - these should not be changed
    xarModSetVar('weather','units','s');
    xarModSetVar('weather','extdays',10);
    
    // Let's register our block
    if (!xarModAPIFunc('blocks','admin','register_block_type',
            array('modName' => 'weather',
                'blockType' => 'current'))) return;
    
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
