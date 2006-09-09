<?php
/*
 * Get admin menu links
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_adminapi_getmenulinks()
{

    if (xarSecurityCheck('EditRelease', 0)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewids'),
                             'title' => xarML('View Registered IDs on the system'),
                             'label' => xarML('View IDs'));

        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewnotes'),
                             'title' => xarML('View Release Notifications'),
                             'label' => xarML('View Notifications'));

        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewdocs'),
                             'title' => xarML('View Release Docs'),
                             'label' => xarML('View Documentation'));

        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Modify the configuration for the module'),
                             'label' => xarML('Modify Config'));

     }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>