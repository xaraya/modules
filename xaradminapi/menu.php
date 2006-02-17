<?php
/**
 * Generate admin menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys Module
 */
/**
 * Generate admin menu
 *
 * Standard function to generate a common admin menu configuration for the module
 *
 * @author the Surveys module development team
 * @author MichelV <michelv@xarayahosting.nl>
 * @TODO MichelV: decide what to place here. BL menus are preferred.
 */
function surveys_adminapi_menu()
{
    /*Initialise the array that will hold the menu configuration */
    $menu = array();
    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('Surveys Administration');
    /* Specify the menu labels to be used in your blocklayout template
     * Preset some status variable
     */
    $menu['status'] = '';

     /* Return the array containing the menu configuration */
    return $menu;
}
?>