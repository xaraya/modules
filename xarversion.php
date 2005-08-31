<?php
/**
 * File: $Id$
 *
 * Example initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author courses module development team
 */
$modversion['name']           = 'Courses';
$modversion['id']             = '179';
$modversion['version']        = '0.0.9';
$modversion['displayname']    = xarML('Courses');
$modversion['description']    = 'Course Offerings and Registration';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'Scot Garder, Michel Vorenhout';
$modversion['contact']        = 'xaraya@sense.nl';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['securityschema'] = array('courses::' => 'Module Name::');
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
$modversion['dependency']     = array(147, 182, 771);
?>
