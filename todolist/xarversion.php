<?php // $Id: s.xarversion.php 1.5 02/12/01 14:28:40+01:00 marcel@hsdev.com $
$modversion['name'] = 'Todolist';
$modversion['id'] = '67';
$modversion['version'] = '0.9.14';
$modversion['displayname']    = xarML('TodoList');
$modversion['description'] = 'Todolist';
$modversion['credits'] = 'docs/credits.txt';
$modversion['help'] = 'docs/help.txt';
$modversion['changelog'] = 'docs/changelog.txt';
$modversion['license'] = 'docs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Jörg Menke/Volodymyr Metenchuk';
$modversion['contact'] = 'http://www.postnuke.ru/';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('todolist::Item' => 'item name::item ID');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
?>