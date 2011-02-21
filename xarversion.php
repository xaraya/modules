<?php
/**
 * Content
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/1015.html
 * @author Ryan Walker <ryan@webcommunicate.net>
 **/
$modversion['name']         = 'content';
$modversion['id']           = '1015';
$modversion['version']      = '0.9.4';
$modversion['displayname']  = xarML('Content');
$modversion['description']  = 'Content';
$modversion['credits']      = 'xardocs/credits.txt';
$modversion['help']         = 'xardocs/help.txt';
$modversion['changelog']    = 'xardocs/changelog.txt';
$modversion['license']      = 'xardocs/license.txt';
$modversion['official']     = 1;
$modversion['author']       = 'potion';
$modversion['contact']      = 'http://www.webcommunicate.net/';
$modversion['admin']        = true;
$modversion['user']         = true;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Content';
$modversion['dependencyinfo'] = array(
		0 => array('name' => 'core', 'version_ge' => '2.1.0', 'version_le' => '2.1.9')
	);
?>
