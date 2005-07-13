<?php
// $Id: xarversion.php,v 1.2 2002/03/09 08:24:24 jgm Exp $
$modversion['name'] = 'xproject';
$modversion['id'] = '665';
$modversion['version'] = '0.1.0';
$modversion['description'] = 'Basic Task/Project Manager';
$modversion['credits'] = 'docs/credits.txt';
$modversion['help'] = 'docs/help.txt';
$modversion['changelog'] = 'docs/changelog.txt';
$modversion['license'] = 'docs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Chad Kraeft';
$modversion['contact'] = 'http://www.ivory-tower.net/';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('xproject::Projects' => 'Project name::Project ID');
									// LOOK AT GROUP LEVEL INSTEAD?
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
?>