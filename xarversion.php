<?php
/**
 * MP3 Jukebox Module - documented module template
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage MP3 Jukebox Module
 * @link http://xaraya.com/index.php
 * @author MP3 Jukebox Module Development Team
 */
$modversion['name']           = 'mp3jukebox'; /* lowercase, no spaces or special chars */
$modversion['id']             = '886';
$modversion['version']        = '1.0.0'; /* three point version number */
$modversion['displayname']    = xarML('MP3 Jukebox');
$modversion['description']    = 'Music Playlist Manager';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'Marty Vance';
$modversion['contact']        = 'http://www.mindsmack.com/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Complete'; /* Complete|Utility|Miscellaneous|Authentication are available options for non-core */
$modversion['category']       = 'Content';  /* Global|Content|User & Group|Miscellaneous available for non-core */

/* Add dependencies var if applicable or remove - example is HTML module using its ID */
$modversion['dependency']     = array(666); /* This module depends on the uploads module */
?>
