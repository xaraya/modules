<?php
/**
 * File: navigator/xarversion.php
 *
 * CHSFnav Version File
 *
 * @copyright (C) 2003 Charles and Helen Schwab Foundation.
 * @license GPL
 * @link http://xavier.schwabfoundation.org
 * @subpackage navigator
 * @author CHSF Dev Team <xavier@schwabfoundation.org>
 */
$modversion['name']           = 'Navigator';
$modversion['id']             = '16466418';
$modversion['version']        = '1.0.2';
$modversion['displayname']    = xarML('Navigator');
$modversion['description']    = 'This module provides category based navigation elements.';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'Charles and Helen Schwab Development Team (Carl P. Corliss and Richard Cave)';
$modversion['contact']        = 'http://xavier.schwabfoundation.org/ (ccorliss@schwabfoundation.org and rcave@schwabfoundation.org)';
$modversion['admin']          = 1;
$modversion['user']           = 0;
$modversion['securityschema'] = array('Navigator::Menu' => 'Menu Name',
                                      'Navigator::Menu Item' => 'Primary Category:Secondary Category',
                                      'Navigator::Block' => 'Block Name');
$modversion['class']          = 'Navigation';
$modversion['category']       = 'Content';
?>
