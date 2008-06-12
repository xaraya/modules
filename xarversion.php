<?php
//$Id: xarversion.php,v 1.4 2003/06/24 16:31:36 roger Exp $

$modversion['name']           = 'calendar';
$modversion['id']             = '7';
$modversion['version']        = '0.2.0';
$modversion['displayname']    = xarML('Calendar');
$modversion['description']    = 'Calendar System';
$modversion['credits']        = 'credits.txt';
$modversion['help']           = 'help.txt';
$modversion['changelog']      = 'changelog.txt';
$modversion['license']        = 'license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Roger Raymond and Xaraya calendar team';
$modversion['contact']        = 'http://xaraya.simiansynapse.com/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
$modversion['dependency']     = array(
                                        8,
                                    30012,
                                    30046,
                                     ); // we need the icalendar module installed
$modversion['dependencyinfo'] = array(
                                    8 => 'icalendar',
                                    30012 => 'math',
                                    30046 => 'listings',
                                     );
$modversion['securityschema'] = array('calendar::event'     => 'Event Title::Event ID',
                                      'calendar::category'  => 'Category Name::Category ID',
                                      'calendar::topic'     => 'Topic Name::Topic ID',
                                      'calendar::user'      => 'User Name::User ID',
                                      'calendar::sharing'   => 'User Name::User ID',
                                      'calendar::'          => '::');

?>
