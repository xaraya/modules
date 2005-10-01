<?php
/**
 * File: $Id$
 *
 * Xaraya POP3 Gateway
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage pop3gateway
 * @author John Cox
*/
$modversion['name'] = 'pop3gateway';
$modversion['id'] = '279';
$modversion['version'] = '1.0.1';
$modversion['displayname']    = xarML('POP3 Gateway');
$modversion['description'] = 'Email to Articles Gateway';
$modversion['official'] = 0;
$modversion['author'] = 'John Cox';
$modversion['contact'] = 'john.cox@wyome.com';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['securityschema'] = array('pop3gateway::' => '::');;
$modversion['class'] = 'Utility';
$modversion['category'] = 'Global';
//$modversion['dependency'] = array(280);
?>
