<?php
/**
 * File: $Id: s.xarinit.php 1.17 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 * 
 * Example initialization functions
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * 
 * @subpackage example
 * @author Example module development team 
 */
$modversion['name']           = 'Example';
$modversion['id']             = '36';
$modversion['version']        = '1.0';
$modversion['description']    = 'Example for new modules';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'Jim McDonald';
$modversion['contact']        = 'http://www.mcdee.net/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['securityschema'] = array('Example::Item' => 'Example item name::Example item ID');
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
?>
