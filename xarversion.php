<?php
/**
 * Courses information file
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */

$modversion['name']           = 'courses';
$modversion['id']             = '179';
$modversion['version']        = '0.6.0';
$modversion['displayname']    = xarML('Courses');
$modversion['description']    = 'Course Offerings and Registration';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'Scot Garder, Michel Vorenhout';
$modversion['contact']        = 'michelv@xarayahosting.nl';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
$modversion['dependency']     = array(147, 182, 771); //Categories; Dynamic Data; Mail module
?>
