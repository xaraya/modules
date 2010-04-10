<?php
/**
 * Admin Main Menu link
 *
 * @package modules
 * @copyright (C) 2004-2010 2skies.com
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html} 
 * @link http://xarigami.com/project/xartinymce
 *
 * @subpackage xartinymce module
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * A utility function  to pass individual menu items to the main administration menu
 *
 * @returns array
 * @return array containing the menulinks for the main admin menu items.
 */
function tinymce_adminapi_getmenulinks()
{
    $menulinks = array();
    
    /* Security Check */
    if (xarSecurityCheck('AdminTinyMCE', 0)) {
  
    $menulinks[] = Array('url' => xarModURL('tinymce','admin','manageconfigs'),
                         'title' => xarML('Modify configuration for instances of tinymce'),
                         'label' => xarML('Manage Editor Instances'),
                         'active' => array('manageconfigs')
                         );
    $menulinks[] = Array('url' => xarModURL('tinymce','admin','manageconfigs',array('action'=>'new')),
                         'title' => xarML('Add new TinyMCE instance'),
                         'label' => xarML('Add Config Instance'),
                         'active' => array()
                         );                         
    $menulinks[] = Array('url' => xarModURL('tinymce','admin','modifyconfig'),
                         'title' => xarML('Modify the general module configuration'),
                         'label' => xarML('Modify Module Config'),
                         'active' => array('modifyconfig')
                         );

    }
  
    return $menulinks;
} 

?>