<?php
/**
 * Dossier Module
 *
 * @package modules
 * @copyright (C) 2002-2009 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
/**
 * generate the common admin menu configuration
 */
function dossier_adminapi_menu()
{
    if (!xarVarFetch('pageName', 'str::', $pageName, NULL, XARVAR_NOT_REQUIRED)) return;
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'jquery',
                        'filename' => 'jquery.pack.js'));
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'jquery',
                        'filename' => 'jquery.compat-1.1.js'));
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'jquery',
                        'filename' => 'thickbox-compressed.js'));
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'dossier',
                        'filename' => 'jquery-relationships.js'));
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'dossier',
                        'filename' => 'jquerycontactlist.js'));

    // Initialise the array that will hold the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarModGetVar('dossier', 'displaytitle');
    
    $menu['showmenu'] = $pageName == "module" ? 0 : 1;

    // Return the array containing the menu configuration
    return $menu;
}

?>
