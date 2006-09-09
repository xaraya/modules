<?php
/**
 * Release Version definitions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */

$modversion['name'] = 'release';
$modversion['id'] = '773';
$modversion['version'] = '0.2.0';
$modversion['displayname']    = xarML('Release');
$modversion['description'] = 'Registration and Release information for themes and modules';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'John Cox';
$modversion['contact'] = 'niceguyeddie@xaraya.com';
$modversion['user'] = 1;
$modversion['admin'] = 1;
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
// Dependent on categories
$modversion['dependency'] = array(147); //categories
?>
