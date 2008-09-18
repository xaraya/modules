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

$modversion['name']           = 'xarpages';
$modversion['id']             = '160';
$modversion['version']        = '0.2.8';
$modversion['displayname']    = xarML('Xarpages');
$modversion['description']    = 'Static pages administration';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'Jason Judge';
$modversion['contact']        = 'http://www.academe.co.uk/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['securityschema'] = array(); //array('Example::Item' => 'Example item name::Example item ID');
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
// 147 = 'categories'; 182 = 'Dynamic Data';
$modversion['dependency']     = array(147, 182);

?>