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
    if (!xarSecurityCheck('ReadReminders')) return;

    if (!xarVarFetch('code',    'str',      $data['code'],       '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'checkbox', $data['confirm'],    false, XARVAR_NOT_REQUIRED)) return;

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

    $email_dates = xarMod::apiFunc('reminders', 'user', 'get_email_dates', array('array' => $data['item']));
	$data['remaining'] = count($email_dates);
	
    $data['authid'] = xarSecGenAuthKey('reminders');

    if ($data['confirm']) {
    	// To remove this reminder we set it inactive
    	$tables = xarDB:getTables();
    	$q = new Query('UPDATE', $tables['reminder_entries']);
    	$q->addfield('state', 2);
    	$q->eq('id', $data['item']['id']);
    	$q->qecho(); exit;
    	$q->run();
    }

    $data['debugmode'] = xarModVars::get('reminders', 'debugmode');
	xarTpl::setPageTemplateName('user_full');
	return $data;
}
?>
