<?php
// $Id: xarversion.php,v 1.5 2002/08/09 18:44:07 johnny Exp $
$modversion['name'] = 'categories';
$modversion['version'] = '2.2';
$modversion['id'] = '147';
$modversion['description'] = 'Categorised data utility';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Jim McDonald';
$modversion['contact'] = 'http://www.mcdee.net/';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['class'] = 'Utility';
$modversion['category'] = 'Content';
$modversion['securityschema'] = array('categories::category' => 'Category name::Category ID',
                                      'categories::item' => 'Category ID:Module ID:Item ID');
?>