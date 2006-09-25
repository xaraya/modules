<?php
$modversion['name'] = 'commerce';
$modversion['id'] = '3006';
$modversion['version'] = '0.5.0';
$modversion['displayname'] = xarML('Commerce');
$modversion['description'] = 'E-commerce application';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Marc Lutolf';
$modversion['contact'] = 'http://www.xaraya.com/';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('Commerce::' => '::');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
// this module depends on the categories module
// this module depends on the xen module
$modversion['dependency'] = array(147,3005,30012);
$modversion['dependencyinfo'] = array(
                                      147 => 'categories',
                                      3005 => 'xen',
                                      30012 => 'math'
                                      );
?>