<?php
/**
 * Example Module - documented module template
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
$modversion['name']           = 'example'; /* lowercase, no spaces or special chars */
$modversion['id']             = '36';      /* register an id on xaraya.com */
$modversion['version']        = '1.5.1'; /* three point version number */
$modversion['displayname']    = 'Example';
$modversion['description']    = 'Documented example and template for new modules';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'Jim McDonald';
$modversion['contact']        = 'http://www.mcdee.net/';
$modversion['admin']          = 1;          /* Offers an admin menu */
$modversion['user']           = 1;          /* Offers a common menu */
$modversion['class']          = 'Complete'; /* Complete|Utility|Miscellaneous|Authentication are available options for non-core */
$modversion['category']       = 'Content';  /* Global|Content|User & Group|Miscellaneous available for non-core */

/* Add dependencies var if applicable - example is HTML module using its ID */
// $modversion['dependency']     = array(779); /* This module depends on the html module */

if (false) { //Bug 6033: Avoid ML calls while getting modversion[] on core init
xarML('Example');
xarML('Documented example and template for new modules');
}
?>