<?php

/**
 * File: $Id$
 *
 * Mag module version information.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @author Jason Judge
 *
 */

$modversion['name']           = 'mag';
$modversion['id']             = '940';
$modversion['version']        = '0.2.0';
$modversion['displayname']    = xarML('Mag');
$modversion['description']    = 'Magazines module';
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
// 182 = Dynamic Data
// TODO: Tags Module
$modversion['dependency']     = array(182);

?>