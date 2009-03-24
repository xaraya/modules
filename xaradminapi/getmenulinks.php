<?php
/**
 * Pass individual menu items to the admin menu
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Netquery Module
 * @link http://xaraya.com/index.php/release/230.html
 */

function netquery_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminNetquery', 0))
    {
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'wiview'),
                             'title' => xarML('View-edit whois TLD/server links'),
                             'label' => xarML('Edit Whois TLDs'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'ptview'),
                             'title' => xarML('View-edit port services/exploits'),
                             'label' => xarML('Edit Port Services'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'flview'),
                             'title' => xarML('View-edit service category flags'),
                             'label' => xarML('Edit Category Flags'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'lgview'),
                             'title' => xarML('View-edit looking glass routers'),
                             'label' => xarML('Edit LG Routers'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'bblogedit'),
                             'title' => xarML('Manage access monitor log entries'),
                             'label' => xarML('Manage Log Entries'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Modify configuration settings'),
                             'label' => xarML('Modify Configuration'));
    }
    if (empty($menulinks)) $menulinks = '';
    return $menulinks;
}
?>