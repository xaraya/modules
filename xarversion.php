<?php
/**
 * Initialization functions
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
$modversion['name']         = 'Images';
$modversion['id']           = '152';
$modversion['version']      = '1.1.0';
$modversion['displayname']  = xarML('Images');
$modversion['description']  = 'Handles basic image manipulation - resizing/cropping/scaling/rotating';
$modversion['credits']      = 'docs/credits.txt';
$modversion['help']         = 'docs/help.txt';
$modversion['changelog']    = 'docs/changelog.txt';
$modversion['license']      = 'docs/license.txt';
$modversion['official']     = true;
$modversion['author']       = 'Carl P. Corliss (carl.corliss@xaraya.com)';
$modversion['contact']      = 'http://www.xaraya.com/';
$modversion['admin']        = true;
$modversion['class']        = 'Utility';
$modversion['category']     = 'Global';
// this module requires the gd extension (for now)
$modversion['extensions']   = array('gd');

?>
