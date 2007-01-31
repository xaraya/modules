<?php
/**
 * xarTinyMCE initialization
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce module
 * @link http://xaraya.com/index.php/release/63.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

$modversion['name'] = 'tinymce';
$modversion['id'] = '63';
$modversion['version'] = '1.1.4';
$modversion['displayname']    = xarML('XarTinyMCE');
$modversion['description'] = 'Integration of TinyMCE, a fast and configurable wysiwyg editor for Xaraya';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Jo Dalle Nogare (jojodee)';
$modversion['contact'] = 'jojodee@xaraya.com';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['class'] = 'Complete';
$modversion['category'] = 'Miscellaneous';
// this module depends on the html module
$modversion['dependency'] = array(779); /* Dependency on html module */

?>