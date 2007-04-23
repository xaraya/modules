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
