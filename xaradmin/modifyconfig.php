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
function weather_admin_modifyconfig()
{
    $partner_id = xarModVars::get('weather','partner_id');
    $license_key = xarModVars::get('weather','license_key');
    $default_location = xarModVars::get('weather','default_location');
    $units = xarModVars::get('weather','units');
    $extdays = xarModVars::get('weather','extdays');
    $cc_cache_time = xarModVars::get('weather','cc_cache_time');
    $ext_cache_time = xarModVars::get('weather','ext_cache_time');
    
    return array(
        'partner_id'=>$partner_id,
        'license_key'=>$license_key,
        'default_location'=>$default_location,
        'cc_cache_time'=>($cc_cache_time/60),
        'ext_cache_time'=>($ext_cache_time/60/60),
        'units'=>$units,
        'extdays'=>$extdays
        );
}

?>