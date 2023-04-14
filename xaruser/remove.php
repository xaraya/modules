<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Suppress the reminders of an entry
 *
 */
function reminders_user_remove()
{
    if (!xarSecurity::check('ReadReminders')) return;

    if (!xarVar::fetch('code',    'str',      $data['code'],       '', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('confirm', 'checkbox', $data['confirm'],    false, xarVar::NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $entries = DataObjectMaster::getObjectList(array('name' => 'reminders_entries'));
        
    // Set all the relevant properties active here
    foreach($entries->properties as $name => $property) {
        if ($property->getDisplayStatus() == DataPropertyMaster::DD_DISPLAYSTATE_DISABLED) continue;
        $entries->properties[$name]->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE);
    }
    $entries->setFieldList();

    $q = $entries->dataquery;
    
    // Only active reminders
    $q->eq('entries.state', 3);
    // The reminder corresponding to this code
    $q->eq('entries.code', $data['code']);

    $items = $entries->getItems();
    $data['item'] = reset($items);
    
    // If we have an active reminder, pass the ID. If not, remove the code
    if (!empty($data['item'])) {
	    $data['itemid'] = $data['item']['id'];
	} else {
    	$data['code'] = '';
    }

    $email_dates = xarMod::apiFunc('reminders', 'user', 'get_remaining_dates', array('array' => $data['item']));
	// By default we also send an email on the due date
	$data['remaining'] = count($email_dates) + 1;
	
    $data['authid'] = xarSec::genAuthKey('reminders');

    if ($data['confirm']) {
    	if (!xarVar::fetch('itemid'           , 'int',    $itemid ,    0,    xarVar::NOT_REQUIRED)) return;
    	if (!xarVar::fetch('remove_recurring' , 'int',    $recurring , 0,    xarVar::NOT_REQUIRED)) return;
    	
    	// Retire the reminder
    	xarMod::apiFunc('reminders', 'admin', 'retire', array('itemid' => $itemid, 'recurring' => $recurring));
    	
    	// Update flags to give the proper message
    	$data['code'] = '';
    	$data['removed'] = 1;
    }

    $data['debugmode'] = xarModVars::get('reminders', 'debugmode');
	xarTpl::setPageTemplateName('user_full');
	return $data;
}
?>
