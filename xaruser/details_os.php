<?php

/**
 * Function for OS details
 *
 * This function collects and packages up the data needed in the
 * OS details page of the stats module
 *
 * @param   none
 * @return  array $data - contains all data needed in the template user-details_os.xd
 */
function stats_user_details_os()
{
    // Security check
    if(!xarSecurityCheck('OverviewStats')) return;

    // Initialize vars
    $picpath = 'modules/stats/xarimages';
    $barlen  = 230;

    $top10 = false;
    $args = compact('top10', 'picpath', 'barlen');
    extract(xarModAPIFunc('stats','user','get_os_data', $args));
    
    // arrange return values
    $data = compact('os', 'picpath');

    // return data to BL template
    return $data;
}

?>