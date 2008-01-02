<?php
/**
 * Purpose of file
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather Module
 * @link http://xaraya.com/index.php/release/662.html
 * @author Weather Module Development Team
 */

/**
 * @author Roger Raymond
 */
function weather_currentblock_init()
{
    return array(
        'nocache'     => 1, /* no cache by default (if block caching is enabled) */
    );
}

function weather_currentblock_info()
{
    return array(
        'text_type' => 'Current',
        'module' => 'weather',
        'text_type_long' => 'Current Conditions',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
        );
}

function weather_currentblock_display($blockinfo)
{
    // Make sure we can view this block
    if(!xarSecurityCheck('ReadWeatherBlock',1,'Block',"All:" . $blockinfo['name'] . ":All",'All')) return;
    
    // Get variables from content block
    $vars = unserialize($blockinfo['content']);
    $w = xarModAPIFunc('weather','user','factory');

    $blockinfo['content'] = array('wData'=>$w->ccData());

    return $blockinfo;
}
?>