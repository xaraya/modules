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
$modversion['name']         = 'crispbb';
$modversion['id']           = '970';
$modversion['version']      = '2.0.0';
$modversion['displayname']  = xarML('crispBB');
$modversion['description']  = 'Feature rich forum module for Xaraya';
$modversion['credits']      = 'xardocs/credits.txt';
$modversion['help']         = 'xardocs/help.txt';
$modversion['changelog']    = 'xardocs/changelog.txt';
$modversion['license']      = 'xardocs/license.txt';
$modversion['official']     = 1;
$modversion['author']       = 'crisp <crisp@crispcreations.co.uk>';
$modversion['contact']      = 'http://crispcreations.co.uk/';
$modversion['admin']        = true;
$modversion['user']         = true;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Content';
$modversion['dependency'] = array(
                                  147,177
                                  );
$modversion['dependencyinfo'] = array(
                                      147  => 'categories',
                                      177  => 'hitcount',
                                      );
?>