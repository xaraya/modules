<?php
/**
 * Overview for Weather
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather
 * @link http://xaraya.com/index.php/release/662.html
 */

/**
 * Overview displays standard Overview page
 */
function weather_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('weather', 'admin', 'main', $data, 'main');
}

?>