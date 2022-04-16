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
 *
 * Version information
 *
 */
    $modversion['name']           = 'reminders';
    $modversion['id']             = '30227';
    $modversion['version']        = '1.0.0';
    $modversion['displayname']    = xarML('Reminders');
    $modversion['description']    = xarML('Sends email reminders to users');
    $modversion['credits']        = 'credits.txt';
    $modversion['help']           = 'help.txt';
    $modversion['changelog']      = 'changelog.txt';
    $modversion['license']        = 'license.txt';
    $modversion['official']       = false;
    $modversion['author']         = 'Marc Lutolf';
    $modversion['contact']        = 'marc@luetolf-carroll.com';
    $modversion['admin']          = true;
    $modversion['user']           = true;
    $modversion['class']          = 'Complete';
    $modversion['category']       = 'Miscellaneous';
    $modversion['securityschema'] = array();
    $modversion['dependency']     = array();
	$modversion['dependencyinfo'] = array(
									0 => array(
											'name' => 'Xaraya Core',
											'version_ge' => '2.4.0'
										 ),
									189 => array(
											'name' => 'scheduler',
											'displayname' => 'Scheduler module',
											'minversion' => '2.0.0'
										 ),
									30064 => array(
											'name' => 'mailer',
											'displayname' => 'Mailer module',
											'minversion' => '1.0.0'
										 ),
									30066 => array(
											'name' => 'ckeditor',
											'displayname' => 'CKEditor module',
											'minversion' => '1.0.0'
										 ),
									);
?>
