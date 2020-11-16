<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/41.html
 */
/**
 * Overview function that displays the standard Overview page
 *
 */
function ratings_admin_overview()
{
    /* Security Check */
    if (!xarSecurity::check('AdminRatings', 0)) {
        return;
    }

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTpl::module('ratings', 'admin', 'main', $data, 'main');
}
