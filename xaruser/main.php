<?php
/**
 * The main user function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * The main user function
 *
 * @return array $data An array with the data for the template
 */
function weather_user_main()
{
    /* Security check */
    if (!xarSecurityCheck('ViewWeather')) return;

    /* Default view */
    $w = xarModAPIFunc('weather','user','factory');
    return array(
        'wData'=>$w->ccData(),
        'eData'=>$w->forecastData()
        );

    /* success so return true */
    return true;
}
?>