<?php
/*
 * Get all user menu links
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */ 
function release_userapi_getmenulinks()
{
    if (xarSecurityCheck('OverviewRelease', 0)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'view'),
                             'title' => xarML('View all extensions IDs'),
                             'label' => xarML('View Registration'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'viewnotes'),
                             'title' => xarML('View all extensions releases'),
                             'label' => xarML('Recent Releases'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'addid'),
                             'title' => xarML('Add an extension ID so it will not be duplicated'),
                             'label' => xarML('Add Registration'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'addnotes'),
                             'title' => xarML('Add an extension release note'),
                             'label' => xarML('Add Release Notes'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'adddocs'),
                             'title' => xarML('Add an extension documentation'),
                             'label' => xarML('Add Documentation'));

    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>