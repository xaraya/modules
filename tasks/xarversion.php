<?php

/**
 * File: $Id$
 *
 * Tasks module info
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * 
 * @subpackage tasks
 * @author Chad Kraeft
*/

$modversion['name'] = 'tasks';
$modversion['id'] = '667';
$modversion['version'] = '0.2.0';
$modversion['description'] = 'Basic Task/ Manager';
$modversion['credits'] = 'docs/credits.txt';
$modversion['help'] = 'docs/help.txt';
$modversion['changelog'] = 'docs/changelog.txt';
$modversion['license'] = 'docs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Chad Kraeft';
$modversion['contact'] = 'http://www.ivory-tower.net/';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('tasks::' => ' name:: ID');
									// LOOK AT GROUP LEVEL INSTEAD?
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
?>
