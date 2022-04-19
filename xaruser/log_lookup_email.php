<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
 
function reminders_user_log_lookup_email($args)
{
    // Xaraya security
    if (!xarSecurityCheck('ManageReminders')) return;
    xarTpl::setPageTitle('Log Lookup Email');

    if (!xarVarFetch('confirm',     'int', $confirm,            0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('code',        'str', $code,               '', XARVAR_NOT_REQUIRED)) return;

# --------------------------------------------------------
#
# Unpack the code that was passed
#
	if (empty($code)) die(xarML('No code passed'));
	
	$args['params'] = unserialize(base64_decode($code));
	
	$data['lookup_id'] = $args['params']['lookup_id'];
	$data['owner_id'] = $args['params']['owner_id'];
	$data['subject'] = $args['params']['lookup_subject'];
	$data['message'] = $args['params']['lookup_message'];
	
# --------------------------------------------------------
#
# Log that the email was sent
#
	sys::import('xaraya.structures.query');
	$tables = xarDB::getTables();
	$q = new Query('INSERT', $tables['reminders_lookup_history']);
	$q->addfield('lookup_id',   (int)$data['lookup_id']);
	$q->addfield('owner_id',    (int)$data['owner_id']);
	$q->addfield('date',        time());
	$q->addfield('subject',     $data['subject']);
	$q->addfield('message',     $data['message']);
	$q->addfield('promised',    1);
	$q->addfield('timecreated', time());
	$q->run();
    
    return array();
}
?>