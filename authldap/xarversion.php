<?php 
$modversion['name'] = 'AuthLDAP';
$modversion['id'] = '50';
$modversion['version'] = '1.0.0';
$modversion['description'] = 'Xaraya LDAP authentication module';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Andreas Jeitler | Chris Dudley | Richard Cave';
$modversion['contact'] = 'ajeitler@edu.uni-klu.ac.at | miko@xaraya.com | rcave@xaraya.com';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['securityschema'] = array('authldap::' => '::');
$modversion['class'] = 'Miscellaneous';
$modversion['category'] = 'Global';
// this module depends on the xarldap module
$modversion['dependency'] = array(25);
?>
