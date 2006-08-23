<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
$modversion['name'] = 'Security';
$modversion['id'] = '270';
$modversion['version'] = '0.9.1';
$modversion['description'] = 'Security provides unix style privileges for xaraya items.';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Brian McGilligan';
$modversion['contact'] = 'brian@mcgilligan.us';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['securityschema'] = array('security::All' => '::');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Global';
$modversion['dependency'] = array();
?>