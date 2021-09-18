<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
$modversion['name']         = 'publications';
$modversion['id']           = '30065';
$modversion['version']      = '2.0.0';
$modversion['displayname']  = xarML('Publications');
$modversion['description']  = xarML('Manage publications on a Xaraya site');
$modversion['credits']      = '';
$modversion['help']         = '';
$modversion['changelog']    = '';
$modversion['license']      = '';
$modversion['official']     = 1;
$modversion['author']       = 'M. Lutolf';
$modversion['contact']      = 'http://www.netspan.ch/';
$modversion['admin']        = true;
$modversion['user']         = true;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Content';
$modversion['dependencyinfo'] = [
                                    0 => [
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.2.0',
                                         ],
                                    30066 => [
                                            'name' => 'ckeditor',
                                            'minversion' => '1.0.0',
                                         ],
                                      ];
$modversion['propertyinfo'] = [
                                    30039 => [
                                        'name' => 'language',
                                        ],
                                    30059 => [
                                        'name' => 'datetime',
                                        ],
                                    30099 => [
                                        'name' => 'pager',
                                        ],
                                    30100 => [
                                        'name' => 'listing',
                                        ],
                                    30101 => [
                                        'name' => 'codemirror',
                                        ],
                                    30122 => [
                                        'name' => 'iconcheckbox',
                                        ],
                                    30123 => [
                                        'name' => 'icondropown',
                                        ],
                                    ];
