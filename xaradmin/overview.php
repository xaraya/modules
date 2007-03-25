<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html

 */
/**
 * Overview function that displays the standard Overview page
 *
 */
function ratings_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminRatings',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTplModule('ratings', 'admin', 'main', $data,'main');
}

?>
