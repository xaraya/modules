<?php

/**
 * Get the month with the most and the least site hits
 *
 * @param   $months array - contains the list of hits per month from the api
 * @return  array - month and absolute site hits
 */
function stats_userapi_gettopmonth(&$months)
{
    $data = array();

    foreach ($months as $key => $month) {
        if (!isset($data['worst'])) {
            $data['worst'] = $key;
            $data['leasthits'] = $month['abs'];
            $data['best'] = $key;
            $data['mosthits'] = $month['abs'];
        }
        if ($month['abs'] < $data['leasthits']) {
            $data['leasthits'] = $month['abs'];
            $data['worst'] = $key;
        }
        if ($month['abs'] > $data['mosthits']) {
            $data['mosthits'] = $month['abs'];
            $data['best'] = $key;
        }
    }

    return $data;
}

?>
