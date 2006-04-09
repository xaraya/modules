<?php
/**
 * Helpdesk Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
// Is Based On:
/********************************************************/
/* Dimensionquest Help Desk                             */
/*  Development by:                                     */
/*     Burke Azbill - burke@dimensionquest.net          */
/*                                                      */
/* This program is opensource so you can do whatever    */
/* you want with it.                                    */
/*                                                      */
/* http://www.dimensionquest.net                        */
/********************************************************/

$modversion['id'] = '910';
$modversion['name'] = 'helpdesk';
$modversion['version'] = '0.7.2';
$modversion['displayname'] = xarML('HelpDesk');
$modversion['description'] = 'HelpDesk Module';
$modversion['credits'] = 'docs/credits.txt';
$modversion['help'] = 'docs/help.txt';
$modversion['changelog'] = 'docs/changelog.txt';
$modversion['license'] = 'docs/license.txt';
$modversion['official'] = 0;
$modversion['author'] = 'Brian McGilligan';
$modversion['contact'] = 'brian@envisionnet.net';
$modversion['user'] = 1;
$modversion['admin'] = 1;
$modversion['securityschema'] = array('helpdesk::' => '::');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';

/*
    269 - Owner Module
    270 - Security Module
*/
$modversion['dependency'] = array(14, 147, 182, 269, 270);
?>
