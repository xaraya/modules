<?php
/**
 * File: $Id$
 *
 * Xaraya Google Search
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Google Search Module
 * @author John Cox
*/
$modversion['name'] = 'googlesearch';
$modversion['id'] = '809';
$modversion['version'] = '1.1.0';
$modversion['description'] = 'Queries Google Search terms and retrieves cached pages VIA Soap';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 0;
$modversion['author'] = 'John Cox';
$modversion['contact'] = 'niceguyeddie@xaraya.com';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('googlesearch::' => '::');;
$modversion['class'] = 'Complete';
$modversion['category'] = 'Miscellaneous';
// this module depends on the soapserver module
//$modversion['dependency'] = array(748);
?>