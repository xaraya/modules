<?php

/**
 * Get the hour with the most and the least site hits
 *
 * @param   $hours array - contains the list of hits per hour from the api
 * @return  array - hour and absolute site hits
 */
function stats_userapi_gettophour(&$hours)
{
    $data = array();
    $data['worst']     = 0;
    $data['leasthits'] = $hours[0]['abs'];
    $data['best']      = $data['worst'];
    $data['mosthits']  = $data['leasthits'];

    foreach ($hours as $key => $hour) {
        if ($hour['abs'] < $data['leasthits']) {
            $data['leasthits'] = $hour['abs'];
            $data['worst'] = $key;
        }
        if ($hour['abs'] > $data['mosthits']) {
            $data['mosthits'] = $hour['abs'];
            $data['best'] = $key;
        }
    }

    return $data;
}

?>