<?php

function weather_currentblock_init()
{
    return true;
}

function weather_currentblock_info()
{
    return array('text_type' => 'Forecast',
                 'module' => 'weather',
                 'text_type_long' => 'Current Conditions');
}


function weather_currentblock_display($blockinfo)
{
    // Make sure we can view this block
    //if(!xarSecurityCheck('ViewCurrentConditions',1,'Block',"All:" . $blockinfo['title'] . ":All",'All')) return;
    
    // Get variables from content block
    $vars = unserialize($blockinfo['content']);
    
    $w =& xarModAPIFunc('weather','user','factory');
    $w->setExtraParams();
    //return array('wData'=>$w->ccData());
    $blockinfo['content'] = xarTplBlock('weather', 'currentconditions', array('wData'=>$w->ccData()));
    return $blockinfo;
}

// Perhaps there will be stuff to set up here later, but it'll use the defaults of the
// main module for now
function weather_currentblock_modify($blockinfo)
{
    return $blockinfo;
}

function weather_currentblock_update($blockinfo)
{
    return $blockinfo;
}
?>