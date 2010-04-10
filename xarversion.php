<?php
/**
 * xarTinyMCE initialization
 *
 * @package modules
 * @copyright (C) 2004-2010 2skies.com
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html} 
 * @link http://xarigami.com/project/xartinymce
 *
 * @subpackage xartinymce module
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

$modversion['name']         = 'tinymce';
$modversion['directory']    = 'tinymce';
$modversion['id']           = '63';
$modversion['version']      = '3.0.4';
$modversion['displayname']  = 'XarTinyMCE';
$modversion['description']  = 'Integration of TinyMCE, a fast and configurable wysiwyg editor for Xaraya';
$modversion['credits']      = 'xardocs/credits.txt';
$modversion['help']         = 'xardocs/readme.txt';
$modversion['changelog']    = 'xardocs/changelog.txt';
$modversion['license']      = 'xardocs/license.txt';
$modversion['official']     = 1;
$modversion['author']       = 'Jo Dalle Nogare (jojodee)';
$modversion['contact']      = 'http://xarigami.com';
$modversion['admin']        = 1;
$modversion['user']         = 0;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Miscellaneous';
$modversion['dependency']   = array(779); /* Dependency on html module */
if (false) { //Load and translate once
    xarML('xarTinymce');
    xarML('Integration of TinyMCE, a fast and configurable wysiwyg editor for Xaraya');
}
?>