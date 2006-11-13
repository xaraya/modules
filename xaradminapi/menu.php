<?php
/**
 * Generate admin menu
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */
/**
 * Generate admin menu
 *
 * Standard function to generate a common admin menu configuration for the module
 *
 * @author the JpGraph module development team
 */
function jpgraph_adminapi_menu()
{
    /*Initialise the array that will hold the menu configuration */
    $menu = array();
    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('JpGraph Administration');
    /* Specify the menu labels to be used in your blocklayout template
     * Preset some status variable
     */
    $menu['status'] = '';

     /* Return the array containing the menu configuration */
    return $menu;
}
?>