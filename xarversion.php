<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
$modversion['name']           = 'crispbb';
$modversion['id']             = '970';
$modversion['version']        = '0.8.5';
$modversion['displayname']    = 'crispBB';
$modversion['description']    = 'crispBB Forum Module';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'crisp <crisp@crispcreations.co.uk>';
$modversion['contact']        = 'http://crispcreations.co.uk';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
$modversion['dependencyinfo'] = array(
    0   => array('name' => 'core', 'version_ge' => '1.2.0', 'version_le' => '1.9.9'),
    147 => array('name' => 'categories', 'version_ge' => '2.4.0'),
    177 => array('name' => 'hitcount'),
);
if (false) { //bug 6033
xarML('crispBB');
xarML('crispBB Forum Module');
}
?>