<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */
/**
    utility function pass individual menu items to the main menu

    @returns array
    @return array containing the menulinks for the main menu items.
*/
function gallery_adminapi_getmenulinks()
{
    // Security Check
    $menulinks = array();
    if( !Security::check(SECURITY_ADMIN, 'gallery', 0, 0, false) )
    {
        $menulinks[] = Array(
            'url'   => xarModURL('gallery', 'admin', 'main'),
            'title' => xarML('Overview'),
            'label' => xarML('Overview')
        );
        $menulinks[] = Array(
            'url'   => xarModURL('gallery', 'admin', 'view'),
            'title' => xarML('View'),
            'label' => xarML('View')
        );
        $menulinks[] = Array(
            'url'   => xarModURL('gallery', 'admin', 'modifyconfig'),
            'title' => xarML('Modify Config'),
            'label' => xarML('Modify Config')
        );
    }

    return $menulinks;
}
?>
