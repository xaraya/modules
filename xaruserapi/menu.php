<?php
/**
 * Generate the common menu configuration
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
 * Generate the common menu configuration
 *
 * @author the PHPlot module development team
 */
function phplot_userapi_menu()
{
    /* Initialise the array that will hold the menu configuration */
    $menu = array();

    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('PHPlot');

    /* Specify the menu items to be used in your blocklayout template */
    $menu['menulabel_view'] = xarML('View phplot items');
    $menu['menulink_view'] = xarModURL('phplot', 'user', 'view');


     /* Return the array containing the menu configuration */
    return $menu;
}
?>