<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author Brian McGilligan
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function helpdesk_userapi_getmenulinks()
{
    $modid = (int) xarModGetIDFromName('helpdesk');
    $menulinks = array();

    // Security Check
    if( Security::check(SECURITY_READ, $modid, 0, 0, false) )
    {
        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'user', 'main'),
            'page' => 'main',
            'title' => xarML('Main Page'),
            'label' => xarML('Main Page')
        );
    }

    // Security Check
    if( Security::check(SECURITY_ADMIN, $modid, 0, 0, false) )
    {
        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'admin','main'),
            'page' => 'main',
            'type' => 'admin',
            'title' => xarML('Administration'),
            'label' => xarML('Administration')
        );
    }

    // Security Check
    if( Security::check(SECURITY_WRITE, $modid, TICKET_ITEMTYPE, 0, false) )
    {
        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'user', 'new'),
            'page' => 'new',
            'title' => xarML('New Ticket'),
            'label' => xarML('New Ticket')
        );
    }

    if( Security::check(SECURITY_READ, $modid, TICKET_ITEMTYPE, 0, false) )
    {
        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'user', 'search'),
            'page' => 'search',
            'title' => xarML('Search'),
            'label' => xarML('Search')
        );

        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'user', 'view'),
            'page' => 'view',
            'title' => xarML('View Tickets'),
            'label' => xarML('View Tickets')
        );
    }

    return $menulinks;
}
?>