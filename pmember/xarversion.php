<?php
/**
 * File: $Id$
 * 
 * Paid Membership table definitions function
 * 
 * @copyright (C) 2003 by the Wyome Consulting, LLC
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.wyome.com
 * @subpackage pmember
 * @author John Cox <john.cox@wyome.com>
 */ 
$modversion['name']           = 'Paid Membership';
$modversion['id']             = '714';
$modversion['version']        = '1.0.0';
$modversion['description']    = 'Allow paid membership to a different user group to your site via PayPal or other IPN Service.';
$modversion['credits']        = '';
$modversion['help']           = '';
$modversion['changelog']      = '';
$modversion['license']        = '';
$modversion['official']       = 0;
$modversion['author']         = 'John Cox';
$modversion['contact']        = 'http://www.wyome.com';
$modversion['admin']          = 1;
$modversion['user']           = 0;
$modversion['securityschema'] = array('pmember::Item' => 'Module ID:Item Type:Item ID');
$modversion['class']          = 'Utility';
$modversion['category']       = 'Users & Groups';
// this module depends on the categories module
$modversion['dependency']     = array(806, 805);
?>