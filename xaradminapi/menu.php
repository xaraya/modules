<?php
/**
 * Admin Main Menu 
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce module
 * @link http://xaraya.com/index.php/release/63.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Generate the common admin menu configuration
 */
function tinymce_adminapi_menu()
{
    /*  Initialise the array that will hold the menu configuration */
    $menu = array();
    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('TinyMCE Administration');
    /* Specify the menu labels to be used in your blocklayout template
     * Preset some status variable
     */
    $menu['status'] = '';

    return $menu;
} 

?>
