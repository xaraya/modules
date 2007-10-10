<?php
/**
 * Common admin menu
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Generate the common admin menu configuration
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
function sitecontact_adminapi_menu()
{
    /* Initialise the array that will hold the menu configuration */
    $menu = array();

    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('SiteContact Administration');

    /* Specify the menu labels to be used in your blocklayout template
     * Preset some status variable
     */
    $menu['status'] = '';

    return $menu;
} 
?>