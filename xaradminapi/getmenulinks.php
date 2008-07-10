<?php
/**
 * Admin Main Menu link
 *
 * @package modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce module
 * @copyright (C) 2002-2008 2skies.com
 * @link http://xarigami.com/projects/xartinymce
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Utility function pass individual menu items to the main menu
 *
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function tinymce_adminapi_getmenulinks()
{
    /* Security Check */
    if (xarSecurityCheck('AdminTinyMCE', 0)) {
  
       $menulinks[] = Array('url' => xarModURL('tinymce','admin','modifyconfig'),
                            'title' => xarML('Modify the configuration for the module'),
                            'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)) {
        $menulinks = '';
    }
    /* The final thing that we need to do in this function is return the values back
     * to the main menu for display.
     */
    return $menulinks;
} 

?>