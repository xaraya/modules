<?php

/**
 * Function for browser details
 *
 * This function collects and packages up the data needed in the
 * browser details page of the stats module
 *
 * @param   none
 * @return  array $data - contains all data needed in the template user-details_browser.xd
 */
function stats_user_details_browser()
{
    // Security check
    if(!xarSecurityCheck('OverviewStats')) return;

    // Initialize vars
    $picpath = 'modules/stats/xarimages';
    $barlen  = 230;

    $top10 = false;
    $args = compact('top10', 'picpath', 'barlen');
    extract(xarModAPIFunc('stats','user','get_browser_data',$args));
    
    // arrange return values
    $data = compact('browsers', 'picpath');

    // return data to BL template
    return $data;
}    

?>