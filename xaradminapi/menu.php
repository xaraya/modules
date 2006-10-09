<?php
/**
 * Generate admin menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
 
function xtasks_adminapi_menu()
{ 
    /*Initialise the array that will hold the menu configuration */
    $menu = array();
    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('xTasks Administration');
    /* Specify the menu labels to be used in your blocklayout template
     * Preset some status variable
     */
     
    $menu['statusmsg'] = '';
     
    return $menu;
} 
?>