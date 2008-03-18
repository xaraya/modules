<?php

/**
 * File: $Id$
 *
 * Xarpages version information.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarpages
 * @author Jason Judge
 */

$modversion['name']           = 'ievents';
$modversion['id']             = '9995'; // TODO: register new ID
$modversion['version']        = '0.1.1';
$modversion['displayname']    = xarML('IEvents');
$modversion['description']    = 'Events module';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'Jason Judge';
$modversion['contact']        = 'http://www.consil.co.uk/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['securityschema'] = array(); //array('Example::Item' => 'Example item name::Example item ID');
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
// 147 = 'categories'; 182 = 'Dynamic Data';
$modversion['dependency']     = array(147, 182);

?>