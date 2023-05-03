<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Display an item of the event object
 *
 */

function calendar_user_display()
{
    if (!xarSecurity::check('ReadCalendar')) return;

    if (!xarVar::fetch('itemid',  'int',  $data['itemid'], 0, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('page',  'str:1',  $data['page'], 'week', xarVar::NOT_REQUIRED)) return;
    $data['object'] = DataobjectMaster::getObject(array('name' => 'calendar_event'));
    $data['object']->getItem(array('itemid' => $data['itemid']));        
    $data['tplmodule'] = 'calendar';
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
?>