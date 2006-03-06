<?php
/**
 * Xaraya Google Search
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Google Search Module
 * @link http://xaraya.com/index.php/release/809.html
 * @author John Cox
 */
function googlesearch_adminapi_getmenulinks()
{
    $menulinks[] = Array('url'   => xarModURL('googlesearch',
                                              'admin',
                                              'getcached'),
                          'title' => xarML('Retrieve Cached Google pages that match a query'),
                          'label' => xarML('Get Cached Pages'));
    $menulinks[] = Array('url'   => xarModURL('googlesearch',
                                              'admin',
                                              'managecached'),
                          'title' => xarML('Manage Cached Google pages'),
                          'label' => xarML('Manage Cached Pages'));
    $menulinks[] = Array('url'   => xarModURL('googlesearch',
                                              'admin',
                                              'collectcached'),
                          'title' => xarML('Collect Stored, Cached Google pages'),
                          'label' => xarML('Collect Cached Pages'));

    if(xarSecurityCheck('Admingooglesearch')) {
        $menulinks[] = Array('url'   => xarModURL('googlesearch',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Edit the googlesearch Configuration'),
                              'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>