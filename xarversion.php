<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */

$modversion['name'] = 'Gallery';
$modversion['id'] = '476';
$modversion['version'] = '0.9.7';
$modversion['description'] = 'The Xaraya Gallery module is a basic photo album management tool.';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 0;
$modversion['author'] = 'Brian McGilligan';
$modversion['contact'] = 'brian@mcgilligan.us';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('gallery::All' => '::');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';

// security and images
$modversion['dependency'] = array(270, 152, 785);
?>