<?php

/**
 * Get the year with the most and the least site hits
 *
 * @param   $years array - contains the list of hits per year from the api
 * @return  array - year and absolute site hits
 */
function stats_userapi_gettopyear(&$years)
{
    $data = array();

    foreach ($years as $key => $year) {
        if (!isset($data['worst'])) {
            $data['worst'] = $key;
            $data['leasthits'] = $year['abs'];
            $data['best'] = $key;
            $data['mosthits'] = $year['abs'];
        }
        if ($year['abs'] < $data['leasthits']) {
            $data['leasthits'] = $year['abs'];
            $data['worst'] = $key;
        }
        if ($year['abs'] > $data['mosthits']) {
            $data['mosthits'] = $year['abs'];
            $data['best'] = $key;
        }
    }

    return $data;
}

?>
