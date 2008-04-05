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
function weather_user_modifyconfig()
{
    $default_location = xarModUserVars::get('weather','default_location');
    $units = xarModUserVars::get('weather','units');
    $extdays = xarModUserVars::get('weather','extdays');
    
    return array(
        'default_location'=>$default_location,
        'units'=>$units,
        'extdays'=>$extdays
        );
}
?>