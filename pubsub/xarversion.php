<?php 
$modversion['name'] = 'Pubsub';
$modversion['id'] = '181';
$modversion['version'] = '1.0';
$modversion['description'] = 'Allow users to subscribe to updates to events';
$modversion['official'] = 1;
$modversion['author'] = 'Chris Dudley';
$modversion['contact'] = 'miko@xaraya.com';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('pubsub::item' => 'Pubsub ID:Event ID:Handling ID:Template ID');
$modversion['class'] = 'Utility';
$modversion['category'] = 'Global';
?>
