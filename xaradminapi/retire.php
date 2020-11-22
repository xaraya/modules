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
 * Set a reminder inactive and spawn a new one if needed
 *
 */

function reminders_adminapi_retire($args)
{
	// We need an itemid
	if (!isset($args['itemid'])) return true;
	
	// To remove this reminder we set it inactive
	$tables = xarDB::getTables();
	$q = new Query('UPDATE', $tables['reminders_entries']);
	$q->addfield('state', 1);
	$q->eq('id', $$args['itemid']);
	$q->run();
	
	// If we keep recurring reminders, then we need to spawn a new reminder from this one
	if ($args['recurring'] == 1) {
		$entry = DataObjectMaster::getObject(array('name' => 'reminders_entries'));
		$item = $entry->getItem(array('itemid' => $itemid));
		$spawned = xarMod::apiFunc('reminders', 'admin', 'spawn', array('object' => $entry));
		if (!$spawned) {
			return xarTpl::module('reminders','user','errors',array('layout' => 'not_spawned'));
		}
	}

	return true;
}
?>