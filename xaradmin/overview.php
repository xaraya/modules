<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage PHPlot Module
 * @link http://xaraya.com/index.php/release/818.html
 * @author PHPlot Module Development Team
 */
/**
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 *
 * @author the PHPlot module development team
 * @return array xarTplModule with $data containing template data
 * @since 3 Sept 2005
 */
function phplot_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminPHPlot',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTplModule('phplot', 'admin', 'main', $data,'main');
}

?>
