<?php
/**
* Get menu items for adminpanels
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Get menu items for adminpanels
*
* @author  Curtis Farnham <curtis@farnham.com>
* @access  public
* @return  array
* @returns list of menu links
*/
function files_adminapi_getmenulinks()
{
    // initialize list
    $menulinks = array();

    // overview link
    if (xarSecurityCheck('AddFiles', 0)) {
        $menulinks[] = array('url' => xarModURL('files','admin','overview'),
            'title' => xarML('Files Overview'),
            'label' => xarML('Overview'));
    }

    // modifyconfig link
    if (xarSecurityCheck('AdminFiles', 0)) {
        $menulinks[] = array('url'   => xarModURL('files', 'admin', 'modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
