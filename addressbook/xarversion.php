<?php
/**
 * File: $Id: xarversion.php,v 1.8 2004/01/24 18:36:52 garrett Exp $
 *
 * AddressBook utility functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

$modversion['name']         = 'AddressBook';
$modversion['id']           = '66417';
$modversion['version']      = '1.2.5';
$modversion['description']  = 'Xaraya Address Book';
$modversion['credits']      = '';
$modversion['help']         = '';
$modversion['changelog']    = 'xardocs/changelog.txt';
$modversion['license']      = 'xardocs/license.txt';
$modversion['official']     = 1;
$modversion['author']       = 'Garrett Hunter';
$modversion['contact']      = 'Garrett Hunter <garret@blacktower.com>';
$modversion['admin']        = 1;
$modversion['user']         = 1;
$modversion['securityschema']   = array('AddressBook::' => '::');
$modversion['class']        = 'Complete';
$modversion['category']     = 'Content';
$modversion['dependency']   = array(775);
?>