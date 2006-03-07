<?php
/**
 *
 * AuthLDAP 
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @author Chris Dudley <miko@xaraya.com>
 * @author Richard Cave <rcave@xaraya.com>
*/

$modversion['name'] = 'AuthLDAP';
$modversion['id'] = '50';
$modversion['version'] = '1.1.0';
$modversion['displayname'] = xarML('AuthLDAP');
$modversion['description'] = 'Xaraya LDAP authentication module';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Andreas Jeitler | Chris Dudley | Richard Cave | Sylvain Beucler';
$modversion['contact'] = 'ajeitler@edu.uni-klu.ac.at | miko@xaraya.com | rcave@xaraya.com | beuc@beuc.net';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['securityschema'] = array('authldap::' => '::');
$modversion['class'] = 'Authentication';
$modversion['category'] = 'Global';
// this module depends on the xarldap module
$modversion['dependency'] = array(25);
// this module requires the ldap extension
$modversion['extensions'] = array('ldap');
?>
