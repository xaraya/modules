<?php
/**
 * Registration module initialization
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/* WARNING
 * Modification of this file is not supported.
 * Any modification is at your own risk and
 * may lead to inability of the system to process
 * the file correctly, resulting in unexpected results.
 */
$modversion['name']           = 'registration';
$modversion['id']             = '30205';
$modversion['version']        = '2.2.0';
$modversion['displayname']    = xarML('User Registration');
$modversion['description']    = 'Standard User Registration';
$modversion['displaydescription'] = xarML('Standard User Registration');
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = true;
$modversion['author']         = 'Jim McDonald, Marco Canini, Jan Schrage, Camille Perinel';
$modversion['contact']        = 'http://www.xaraya.com';
$modversion['admin']          = true;
$modversion['user']           = true;
$modversion['class']          = 'Registration';
$modversion['category']       = 'Users & Groups';
$modversion['dependency']     = array();
$modversion['dependencyinfo'] = array(
		0 => array('name' => 'core', 'version_ge' => '2.2.0', 'version_le' => '2.2.9')
	);
?>