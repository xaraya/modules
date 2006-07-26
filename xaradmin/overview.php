<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sniffer Module
 * @link http://xaraya.com/index.php/release/775.html
 * @author Sniffer Module Development Team
 */
/**
 * Overview function that displays the standard Overview page
 *
 */
function sniffer_admin_overview()
{
    $data=array();

   /* Security Check */
    if (!xarSecurityCheck('AdminSniffer',0)) return $data;

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTplModule('sniffer', 'admin', 'main', $data,'main');
}

?>
