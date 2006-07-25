<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/333.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
$modversion['name'] = 'accessmethods';
$modversion['id'] = '732';
$modversion['version'] = '1.0.0';
$modversion['displayname']    = xarML('Account Access Methods');
$modversion['description'] = 'Managed Website Tool';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'St.Ego';
$modversion['contact'] = 'http://www.miragelab.com/';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['securityschema'] = array('accessmethods::item' => 'Access Method name::Access Method ID');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
/* Add dependencies var if applicable or remove - example is HTML module using its ID */
$modversion['dependency']     = array(66417); /* This module depends on the addressbook module */
?>
