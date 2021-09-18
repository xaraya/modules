<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */


$modversion['name']           = 'calendar';
$modversion['id']             = '7';
$modversion['version']        = '2.0.0';
$modversion['displayname']    = xarML('Calendar');
$modversion['description']    = 'Calendar System';
$modversion['credits']        = 'credits.txt';
$modversion['help']           = 'help.txt';
$modversion['changelog']      = 'changelog.txt';
$modversion['license']        = 'license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Roger Raymond and Xaraya calendar team';
$modversion['contact']        = 'http://xaraya.simiansynapse.com/';
$modversion['admin']          = true;
$modversion['user']           = true;
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
$modversion['dependencyinfo'] = [
                                    0 => [
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.4.0',
                                         ],
                                    8 => [
                                            'name' => 'icalendar',
                                            'displayname' => 'icalendar',
                                            'minversion' => '1.0.0',
                                         ],
                                ];
$modversion['securityschema'] = ['calendar::event'     => 'Event Title::Event ID',
                                      'calendar::category'  => 'Category Name::Category ID',
                                      'calendar::topic'     => 'Topic Name::Topic ID',
                                      'calendar::user'      => 'User Name::User ID',
                                      'calendar::sharing'   => 'User Name::User ID',
                                      'calendar::'          => '::', ];
