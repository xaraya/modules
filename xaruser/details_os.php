<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
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