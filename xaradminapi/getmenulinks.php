<?php
/**
* Get menu items for adminpanels
*
* @package modules
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Get menu items for adminpanels
*
* @author  Curtis Farnham <curtis@farnham.com>
* @access  public
* @return  array of menu links
*/
function highlight_adminapi_getmenulinks()
{
    // initialize menu links array
    $menulinks = array();

    // security check
    if (xarSecurityCheck('AdminHighlight')) {
        $menulinks[] = array(
            'url'   => xarModURL('highlight', 'admin', 'overview'),
            'title' => xarML('Module Overview'),
            'label' => xarML('Overview'));

        $menulinks[] = array(
            'url' => xarModURL('highlight', 'admin', 'languages'),
            'title' => xarML('View the available languages'),
            'label' => xarML('Languages'));

        $menulinks[] = array(
            'url'   => xarModURL('highlight', 'admin', 'modifyconfig'),
            'title' => xarML('Modify the Highlight module configuration'),
            'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
