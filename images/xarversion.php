<?php
// $Id: s.xarversion.php 1.3 02/12/01 14:28:39+01:00 marcel@hsdev.com $
$modversion['name'] = 'Images';
$modversion['id'] = '152';
$modversion['version'] = '1.0';
$modversion['description'] = 'Handle images';
$modversion['credits'] = 'docs/credits.txt';
$modversion['help'] = 'docs/help.txt';
$modversion['changelog'] = 'docs/changelog.txt';
$modversion['license'] = 'docs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Jim McDonald';
$modversion['contact'] = 'http://www.mcdee.net/';
$modversion['admin'] = 1;
$modversion['securityschema'] = array('Images::Category' => 'Category name::Category ID',
                                'Images::Item' => 'Item title::Item ID');
?>