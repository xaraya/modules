<?php
/**
 * AuthLDAP
 * 
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/50.html
 * @author Chris Dudley <miko@xaraya.com>
 * @author Richard Cave <rcave@xaraya.com>
*/

$modversion['name'] = 'AuthLDAP';
$modversion['id'] = '50';
$modversion['version'] = '1.2.0';
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
$modversion['class'] = 'Authentication';
$modversion['category'] = 'Global';
// this module depends on the xarldap module
$modversion['dependency'] = array(25);
$modversion['dependencyinfo']   = array(
                                    0 => array(
                                            'name' => 'core',
                                            'version_ge' => '1.2.0-b1'
                                         ),
                                  );
// this module requires the ldap extension
$modversion['extensions'] = array('ldap');
?>
