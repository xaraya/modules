<?php
/**
 * File: $Id$
 *
 * contains the module information
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage trackback
 * @author John Cox
*/
$modversion['name'] = 'TrackBack';
$modversion['id'] = '183';
$modversion['version'] = '1.0.0';
$modversion['displayname']    = xarML('Trackback');
$modversion['description'] = 'Allows for the sending and recieving of trackbacks';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = '';
$modversion['changelog'] = '';
$modversion['license'] = '';
$modversion['official'] = 0;
$modversion['author'] = 'John Cox';
$modversion['contact'] = 'niceguyeddie@xaraya.com';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['securityschema'] = array('Trackback::' => 'Module Id : Module Page ID : Comment ID');
$modversion['class'] = 'Utility';
$modversion['category'] = 'Content';
// dependancies??
$modversion['requires'] = array(14);
?>