<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Version information
 *
 */
$modversion['name']           = 'payments';
$modversion['id']             = '30052';
$modversion['version']        = '1.0.0';
$modversion['displayname']    = xarML('Payments');
$modversion['description']    = xarML('Manage ebanking/ecommerce payments');
$modversion['credits']        = 'credits.txt';
$modversion['help']           = 'help.txt';
$modversion['changelog']      = 'changelog.txt';
$modversion['license']        = 'license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Marc Lutolf';
$modversion['contact']        = '';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Complete';
$modversion['category']       = 'Commerce';
$modversion['dependency']     = [];
$modversion['securityschema'] = [];
$modversion['dependency'] = [
                                  30064,
                                  ];
$modversion['dependencyinfo'] = [
                                0 => [
                                        'name' => 'Xaraya Core',
                                        'version_ge' => '2.4.0',
                                     ],
                                      ];
$modversion['propertyinfo'] = [
                                    30059 => [
                                        'name' => 'datetime',
                                        ],
                                    30099 => [
                                        'name' => 'pager',
                                        ],
                                    30100 => [
                                        'name' => 'listing',
                                        ],
                                    30143 => [
                                        'name' => 'iban',
                                        ],
                                    ];
