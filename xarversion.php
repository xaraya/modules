<?php
/**
 * Release Version definitions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */

$modversion['name'] = 'release';
$modversion['id'] = '773';
$modversion['version'] = '0.4.1';
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
$modversion['dependency']     = array();
$modversion['dependencyinfo'] = array(
                                0 => array(
                                        'name' => 'Xaraya Core',
                                        'version_ge' => '2.4.0'
                                     ),
                                     /*
                                30065 => array(
                                        'name' => 'publications',
                                        'minversion' => '2.0.0'
                                     ),*/
                                      );
$modversion['propertyinfo'] = array(
                                30059 => array(
                                    'name' => 'datetime',
                                    ),
                                30099 => array(
                                    'name' => 'pager',
                                    ),
                                30100 => array(
                                    'name' => 'listing',
                                    ),
                                );
