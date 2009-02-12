<?php
/**
 * Dossier Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
/**
 * generate the common menu configuration
 */
function dossier_userapi_menu()
{
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'jquery',
                        'filename' => 'jquery.js'));
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'jquery',
                        'filename' => 'jquery.pack.js'));
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'jquery',
                        'filename' => 'jquery.compat-1.1.js'));
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'jquery',
                        'filename' => 'thickbox-compressed.js'));
                        
    // Initialise the array that will hold the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Dossier');

    // Return the array containing the menu configuration
    return $menu;
}

?>
