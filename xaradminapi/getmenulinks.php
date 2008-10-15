<?php
/**
 * IEvents Module
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage IEvents
 * @author Jason Judge.
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the IEvents module development team
 * @return array containing the menulinks for the main menu items.
 */
function ievents_adminapi_getmenulinks()
{
    $menulinks = array();

    $itemtype_events = xarModGetVar('ievents', 'itemtype_events');
    $itemtype_calendars = xarModGetVar('ievents', 'itemtype_calendars');

    if (xarSecurityCheck('EditIEventCal',0,'IEventCal')) {
        $menulinks[] = Array('url'   => xarModURL('ievents','admin','viewcals'),
                              'title' => xarML('View, edit or create Calendars'),
                              'label' => xarML('Manage Calendars'));
    }

    if (xarSecurityCheck('CommentIEventCal',0,'IEventCal')) {

        $menulinks[] = Array('url'   => xarModURL('ievents','admin','modify',
                                  array('itemtype' => $itemtype_calendars)
                              ),
                              'title' => xarML('Create a New Calendar'),
                              'label' => xarML('New Calendar'));
    }
    if (xarSecurityCheck('CommentIEvent',0,'IEvent')) {

        $menulinks[] = Array('url'   => xarModURL('ievents','admin','modify'),
                              'title' => xarML('Create a New Event'),
                              'label' => xarML('New Event'));
    }

    if (xarSecurityCheck('AdminIEventCal',0,'IEventCal')) {
        $menulinks[] = Array('url' => xarModURL('ievents',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify IEvents configuration'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }            

    return $menulinks;
}

?>

