<?php
/**
 *
 * Version information
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Gmaps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  Version information on this module
 *
 * @param none
 * @return version information of this module
 *
 */

$modversion['name']           = 'gmaps';
$modversion['id']             = '30038';
$modversion['version']        = '1.0.0';
$modversion['displayname']    = xarML('Gmaps');
$modversion['description']    = 'Google maps module';
$modversion['credits']        = 'credits.txt';
$modversion['help']           = 'help.txt';
$modversion['changelog']      = 'changelog.txt';
$modversion['license']        = 'license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Marc Lutolf';
$modversion['contact']        = 'http://www.netspan.ch/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Complete';
$modversion['category']       = 'Utility';
$modversion['dependency']     = array();
$modversion['securityschema'] = array();
$modversion['dependency'] = array(147, 3005);
$modversion['dependencyinfo'] = array(
									  147 => 'categories',
									  3005  => 'xen',
									  );
?>