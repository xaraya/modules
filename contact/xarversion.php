<?php
// $Id: s.xarversion.php 1.7 02/12/23 19:15:18-05:00 Scot.Gardner@ws75. $ $Name: <Not implemented> $
$modversion['name'] = 'Contact';
$modversion['id'] = '136';
$modversion['version'] = '0.2.8';
$modversion['displayname']      = xarML('Contact');
$modversion['description'] = 'Xaraya Contact Module';
$modversion['credits'] = 'docs/credits.txt';
$modversion['help'] = 'docs/help.txt';
$modversion['changelog'] = 'docs/changelog.txt';
$modversion['license'] = 'docs/license.txt';
$modversion['offical'] = 1;
$modversion['author'] = 'Scot Gardner';
$modversion['contact'] = 'XarayaGeek@Xaraya.com';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('Contact::' => 'Contact ID::Contact ID',
                                      'Contact::' => 'Persons ID:Module ID:Item ID');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
?>
