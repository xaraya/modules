<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints
 * @link http://xaraya.com/index.php/release/782.html
 */
/**
 * Overview function that displays the standard Overview page
 */
function userpoints_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminUserpointsRank',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTplModule('userpoints', 'admin', 'main', $data,'main');
}

?>
