<?php
/**
 * File: $Id:
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage tinymce
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Utility function pass individual menu items to the main menu
 *
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function tinymce_adminapi_getmenulinks()
{

    // Security Check
    if (xarSecurityCheck('AdminTinyMCE', 0)) {
        $menulinks[] = Array('url' => xarModURL('tinymce',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)) {
        $menulinks = '';
    }
    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;
} 

?>
