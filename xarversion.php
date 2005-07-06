<?php
/**
 * Security
 *
 * @copyright (C) 2003-1005 by Envision Net, Inc.
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 * @link http://www.envisionnet.net
 * @author Brian McGilligan <brian@envisionnet.net>
 *
 * @package Xaraya eXtensible Management System
 * @subpackage Security module
*/

$modversion['name'] = 'Security';
$modversion['id'] = '270';
$modversion['version'] = '0.1.1';
$modversion['description'] = 'Security';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Brian McGilligan';
$modversion['contact'] = 'brian@envisionnet.net';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['securityschema'] = array('security::All' => '::');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Global';
// this module depends on the categories module
$modversion['dependency'] = array(269);
?>