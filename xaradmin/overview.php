<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ie7Module
 * @link http://xaraya.com/index.php/release/107.html
 * @author Roger Keays <roger.keays@ninthave.net>
 */
/**
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 */
function ie7_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminIE7')) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTplModule('ie7', 'admin', 'main', $data,'main');
}

?>