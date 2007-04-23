<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * Get the weekday with the most and the least site hits
 *
 * @param   $weekdays array - contains the list of hits per weekday from the api
 * @return  array - weekday name and absolute site hits
 */
function stats_userapi_gettopweekday(&$weekdays)
{
    // initialize variables
    $data = array();
    $data['worst']     = $weekdays[0]['name'];
    $data['leasthits'] = $weekdays[0]['abs'];
    $data['best']      = $data['worst'];
    $data['mosthits']  = $data['leasthits'];

    // determine maximum and minimum
    foreach ($weekdays as $weekday) {
        if ($weekday['abs'] < $data['leasthits']) {
            $data['leasthits'] = $weekday['abs'];
            $data['worst'] = $weekday['name'];
        }
        if ($weekday['abs'] > $data['mosthits']) {
            $data['mosthits'] = $weekday['abs'];
            $data['best'] = $weekday['name'];
        }
    }

    // return the items
    return $data;
}

?>