<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage HTML Module
 * @link http://xaraya.com/index.php/release/779.html
 * @author John Cox
 */
/**
 * Utility function pass individual menu items to the main menu
 *
 * @access public
 * @author John Cox
 * @author Richard Cave
 * @author the HTML module development team
 * @return array containing the menulinks for the main menu items.
 */
function html_adminapi_getmenulinks()
{
    $menulinks = array();
    if (xarSecurityCheck('AddHTML')) {

        $menulinks[] = Array('url'   => xarModURL('html',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new tag.'),
                              'label' => xarML('Add Tag'));
    }
    if (xarSecurityCheck('AdminHTML')) {
        $menulinks[] = Array('url'   => xarModURL('html',
                                                  'admin',
                                                  'set'),
                              'title' => xarML('Set the allowed tags for use on your site'),
                              'label' => xarML('Set Tags'));
    }

    if (xarSecurityCheck('AddHTML')) {
        $menulinks[] = Array('url'   => xarModURL('html',
                                                  'admin',
                                                  'newtype'),
                              'title' => xarML('Add a new tag type for use on your site.'),
                              'label' => xarML('Add Tag Type'));
    }
    if (xarSecurityCheck('ReadHTML')) {
        $menulinks[] = Array('url'   => xarModURL('html',
                                                  'admin',
                                                  'viewtypes'),
                              'title' => xarML('View and edit tag types.'),
                              'label' => xarML('View Tag Types'));
    }
    if (xarSecurityCheck('AdminHTML')) {
        $menulinks[] = Array('url'   => xarModURL('html',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration of the HTML Module'),
                              'label' => xarML('Modify Config'));
    }
    return $menulinks;
}
?>
