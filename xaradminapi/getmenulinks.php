<?php
/**
 * File: $Id$
 *
 * AuthSSO Administrative Display Functions
 * 
 * @package authentication
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authsso
 * @author Jonn Beames <jsb@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * utility function pass individual menu items to the main menu
 *
 * @author Richard Cave
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function authsso_adminapi_getmenulinks()
{
    // Security check 
    if (xarSecurityCheck('AdminAuthSSO')) {
        $menulinks[] = Array('url'   => xarModURL('authsso',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));
    } else {
        $menulinks = '';
    }

    return $menulinks;
}

?>
