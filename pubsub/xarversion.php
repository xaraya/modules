<?php
/**
 * File: $Id$
 *
 * Pubsub Version vars
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
*/

$modversion['name'] = 'Pubsub';
$modversion['id'] = '181';
$modversion['version'] = '1.3.0';
$modversion['description'] = 'Allow users to subscribe to updates to events';
$modversion['official'] = 1;
$modversion['author'] = 'Chris Dudley,Garrett Hunter';
$modversion['contact'] = 'miko@xaraya.com,garrett@blacktower.com';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['securityschema'] = array('pubsub::item' => 'Pubsub ID:Event ID:Handling ID:Template ID');
$modversion['class'] = 'Utility';
$modversion['category'] = 'Global';
?>
