<?php
/*
 * File: $Id: $
 *
 * Newsletter
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/

$modversion['name'] = 'Newsletter';
$modversion['id'] = '1655';
$modversion['version'] = '1.1.2';
$modversion['displayname']    = xarML('Newsletter');
$modversion['description'] = 'Newsletter publication.';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Richard Cave';
$modversion['contact'] = 'rcave@xaraya.com';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('newsletter::All' => '::');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
// this module depends on the categories module
$modversion['dependency'] = array(147);
?>
